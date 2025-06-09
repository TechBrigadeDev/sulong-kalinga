<?php
// UNUSED
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WeeklyCarePlan;
use App\Models\GeneralCarePlan;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ReportsApiController extends Controller
{
    /**
     * List reports with role-based filtering, pagination, search, and standardized fields.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $roleId = $user->role_id;

        // Search/filter params
        $search = $request->input('search');
        $authorId = $request->input('author_id');
        $beneficiaryId = $request->input('beneficiary_id');
        $reportType = $request->input('report_type'); // 'weekly' or 'general'
        $sort = $request->input('sort', 'desc');
        $sortBy = $request->input('sort_by', 'created_at'); // new: allow sorting by type/author
        $perPage = (int) $request->input('per_page', 15);

        try {
            // Weekly Care Plans
            $weeklyQuery = WeeklyCarePlan::with(['author', 'beneficiary'])
                ->when($search, function ($q) use ($search) {
                    $q->whereHas('author', function ($q2) use ($search) {
                        $q2->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                           ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
                    })->orWhereHas('beneficiary', function ($q2) use ($search) {
                        $q2->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                           ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
                })
                ->when($authorId, fn($q) => $q->where('created_by', $authorId))
                ->when($beneficiaryId, fn($q) => $q->where('beneficiary_id', $beneficiaryId));

            // Role-based filtering for Weekly Care Plans
            if ($roleId == 3) { // Care Worker
                $weeklyQuery->where('created_by', $user->id);
            }

            // General Care Plans
            $generalQuery = GeneralCarePlan::with(['beneficiary'])
                ->when($search, function ($q) use ($search) {
                    $q->orWhereHas('beneficiary', function ($q2) use ($search) {
                        $q2->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                           ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
                })
                ->when($beneficiaryId, fn($q) => $q->where('beneficiary_id', $beneficiaryId));

            // Role-based filtering for General Care Plans
            if ($roleId == 3) { // Care Worker
                $generalQuery->where(function ($q) use ($user) {
                    $q->where('care_worker_id', $user->id)
                      ->orWhere('created_by', $user->id);
                });
            }

            // Apply report type filter
            $weekly = collect();
            $general = collect();
            if (!$reportType || $reportType === 'weekly') {
                $weekly = $weeklyQuery->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'report_id' => $item->id,
                        'report_type' => 'Weekly Care Plan',
                        'author_id' => $item->author_id,
                        'author_first_name' => $item->author->first_name ?? '',
                        'author_last_name' => $item->author->last_name ?? '',
                        'beneficiary_id' => $item->beneficiary_id,
                        'beneficiary_first_name' => $item->beneficiary->first_name ?? '',
                        'beneficiary_last_name' => $item->beneficiary->last_name ?? '',
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'notes' => $item->notes ?? null,
                        'summary' => $item->summary ?? null,
                    ];
                });
            }
            if (!$reportType || $reportType === 'general') {
                $general = $generalQuery->get()->map(function ($item) {
                    // Try to find the author (creator of the beneficiary record)
                    $author = null;
                    if ($item->beneficiary && $item->beneficiary->created_by) {
                        $author = User::find($item->beneficiary->created_by);
                    }
                    return [
                        'id' => $item->id,
                        'report_id' => $item->id,
                        'report_type' => 'General Care Plan',
                        'author_id' => $author->id ?? null,
                        'author_first_name' => $author->first_name ?? '',
                        'author_last_name' => $author->last_name ?? '',
                        'beneficiary_id' => $item->beneficiary_id,
                        'beneficiary_first_name' => $item->beneficiary->first_name ?? '',
                        'beneficiary_last_name' => $item->beneficiary->last_name ?? '',
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'notes' => $item->notes ?? null,
                        'summary' => $item->summary ?? null,
                    ];
                });
            }

            // Merge
            $reports = $weekly->merge($general);

            // Sorting
            if ($sortBy === 'report_type') {
                $reports = $reports->sortBy('report_type', SORT_NATURAL | SORT_FLAG_CASE);
                if ($sort === 'desc') $reports = $reports->reverse();
            } elseif ($sortBy === 'author') {
                $reports = $reports->sortBy(function ($item) {
                    return strtolower($item['author_last_name'] . ' ' . $item['author_first_name']);
                });
                if ($sort === 'desc') $reports = $reports->reverse();
            } else { // Default: created_at
                $reports = $reports->sortBy('created_at');
                if ($sort === 'desc') $reports = $reports->reverse();
            }
            $reports = $reports->values();

            // Pagination (manual, since merged collections)
            $page = (int) $request->input('page', 1);
            $total = $reports->count();
            $paginated = $reports->slice(($page - 1) * $perPage, $perPage)->values();

            return response()->json([
                'success' => true,
                'reports' => $paginated,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => ceil($total / $perPage),
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('ReportsApiController@index error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching reports.'], 500);
        }
    }

    /**
     * Show a single report with standardized fields and role-based access.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $roleId = $user->role_id;

        try {
            // Try Weekly Care Plan first
            $report = WeeklyCarePlan::with(['author', 'beneficiary'])->find($id);
            $type = 'Weekly Care Plan';

            if ($report) {
                // Care worker can only view their own reports
                if ($roleId == 3 && $report->created_by != $user->id) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
                }
            } else {
                // Try General Care Plan
                $report = GeneralCarePlan::with(['beneficiary'])->find($id);
                $type = 'General Care Plan';

                if ($report) {
                    // Care worker can only view if assigned or creator
                    if ($roleId == 3 && !(
                        $report->care_worker_id == $user->id ||
                        ($report->beneficiary && $report->beneficiary->created_by == $user->id)
                    )) {
                        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
                    }
                }
            }

            if (!$report) {
                return response()->json(['success' => false, 'message' => 'Report not found'], 404);
            }

            // Standardize fields
            $author = null;
            if ($type === 'Weekly Care Plan') {
                $author = $report->author;
            } elseif ($type === 'General Care Plan' && $report->beneficiary && $report->beneficiary->created_by) {
                $author = User::find($report->beneficiary->created_by);
            }

            $result = [
                'id' => $report->id,
                'report_id' => $report->id,
                'report_type' => $type,
                'author_id' => $author->id ?? null,
                'author_first_name' => $author->first_name ?? '',
                'author_last_name' => $author->last_name ?? '',
                'beneficiary_id' => $report->beneficiary_id,
                'beneficiary_first_name' => $report->beneficiary->first_name ?? '',
                'beneficiary_last_name' => $report->beneficiary->last_name ?? '',
                'created_at' => $report->created_at,
                'updated_at' => $report->updated_at,
                'notes' => $report->notes ?? null,
                'summary' => $report->summary ?? null,
            ];

            return response()->json([
                'success' => true,
                'report' => $result
            ]);
        } catch (\Throwable $e) {
            Log::error('ReportsApiController@show error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching the report.'], 500);
        }
    }

    /**
     * Update a report (Weekly or General Care Plan) with role-based access.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $roleId = $user->role_id;

        try {
            // Try Weekly Care Plan first
            $report = WeeklyCarePlan::find($id);
            $type = 'Weekly Care Plan';

            if ($report) {
                // Care worker can only update their own reports
                if ($roleId == 3 && $report->created_by != $user->id) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
                }
            } else {
                $report = GeneralCarePlan::find($id);
                $type = 'General Care Plan';

                if ($report) {
                    // Care worker can only update if assigned or creator
                    if ($roleId == 3 && !(
                        $report->care_worker_id == $user->id ||
                        ($report->beneficiary && $report->beneficiary->created_by == $user->id)
                    )) {
                        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
                    }
                }
            }

            if (!$report) {
                return response()->json(['success' => false, 'message' => 'Report not found'], 404);
            }

            // Only allow editing notes/summary fields
            $validator = Validator::make($request->all(), [
                'notes' => 'nullable|string',
                'summary' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $report->fill($request->only(['notes', 'summary']));
            $report->save();

            // Standardize fields for response
            $author = null;
            if ($type === 'Weekly Care Plan') {
                $author = $report->author;
            } elseif ($type === 'General Care Plan' && $report->beneficiary && $report->beneficiary->created_by) {
                $author = User::find($report->beneficiary->created_by);
            }

            $result = [
                'id' => $report->id,
                'report_id' => $report->id,
                'report_type' => $type,
                'author_id' => $author->id ?? null,
                'author_first_name' => $author->first_name ?? '',
                'author_last_name' => $author->last_name ?? '',
                'beneficiary_id' => $report->beneficiary_id,
                'beneficiary_first_name' => $report->beneficiary->first_name ?? '',
                'beneficiary_last_name' => $report->beneficiary->last_name ?? '',
                'created_at' => $report->created_at,
                'updated_at' => $report->updated_at,
                'notes' => $report->notes ?? null,
                'summary' => $report->summary ?? null,
            ];

            return response()->json([
                'success' => true,
                'report' => $result
            ]);
        } catch (\Throwable $e) {
            Log::error('ReportsApiController@update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => 'An error occurred while updating the report.'], 500);
        }
    }
}
