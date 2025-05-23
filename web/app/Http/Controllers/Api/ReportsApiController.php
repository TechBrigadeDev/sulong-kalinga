<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WeeklyCarePlan;
use App\Models\GeneralCarePlan;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportsApiController extends Controller
{
    public function index(Request $request)
    {
        // Search/filter params
        $search = $request->input('search');
        $authorId = $request->input('author_id');
        $beneficiaryId = $request->input('beneficiary_id');
        $reportType = $request->input('report_type'); // 'weekly' or 'general'
        $sort = $request->input('sort', 'desc');

        // Weekly Care Plans
        $weekly = WeeklyCarePlan::with(['author', 'beneficiary'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('author', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%$search%")
                       ->orWhere('last_name', 'like', "%$search%");
                })->orWhereHas('beneficiary', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%$search%")
                       ->orWhere('last_name', 'like', "%$search%");
                });
            })
            ->when($authorId, fn($q) => $q->where('created_by', $authorId))
            ->when($beneficiaryId, fn($q) => $q->where('beneficiary_id', $beneficiaryId))
            ->get()
            ->map(function ($item) {
                return [
                    'report_id' => $item->id,
                    'report_type' => 'Weekly Care Plan',
                    'author_id' => $item->author_id,
                    'author_first_name' => $item->author->first_name ?? '',
                    'author_last_name' => $item->author->last_name ?? '',
                    'beneficiary_id' => $item->beneficiary_id,
                    'beneficiary_first_name' => $item->beneficiary->first_name ?? '',
                    'beneficiary_last_name' => $item->beneficiary->last_name ?? '',
                    'created_at' => $item->created_at,
                    // Add other fields as needed
                ];
            });

        // General Care Plans
        $general = GeneralCarePlan::with(['beneficiary']) // remove 'author'
            ->when($search, function ($q) use ($search) {
                $q->orWhereHas('beneficiary', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%$search%")
                       ->orWhere('last_name', 'like', "%$search%");
                });
            })
            // Remove authorId filter
            ->when($beneficiaryId, fn($q) => $q->where('beneficiary_id', $beneficiaryId))
            ->get()
            ->map(function ($item) {
                return [
                    'report_id' => $item->id,
                    'report_type' => 'General Care Plan',
                    'beneficiary_id' => $item->beneficiary_id,
                    'beneficiary_first_name' => $item->beneficiary->first_name ?? '',
                    'beneficiary_last_name' => $item->beneficiary->last_name ?? '',
                    'created_at' => $item->created_at,
                    // Add other fields as needed
                ];
            });

        // Merge and sort
        $reports = $weekly->merge($general)->sortByDesc('created_at')->values();

        return response()->json([
            'success' => true,
            'reports' => $reports
        ]);
    }

    public function show($id)
    {
        // Try Weekly Care Plan first
        $report = WeeklyCarePlan::with(['author', 'beneficiary'])->find($id);
        $type = 'Weekly Care Plan';

        if (!$report) {
            // Try General Care Plan
            $report = GeneralCarePlan::with(['beneficiary'])->find($id);
            $type = 'General Care Plan';
        }

        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Report not found'], 404);
        }

        return response()->json([
            'success' => true,
            'report_type' => $type,
            'report' => $report
        ]);
    }

    public function update(Request $request, $id)
    {
        // Try Weekly Care Plan first
        $report = WeeklyCarePlan::find($id);
        $type = 'Weekly Care Plan';

        if (!$report) {
            $report = GeneralCarePlan::find($id);
            $type = 'General Care Plan';
        }

        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Report not found'], 404);
        }

        // Example: Only allow editing notes/summary fields
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
            'summary' => 'nullable|string',
            // Add other editable fields as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $report->fill($request->only(['notes', 'summary']));
        $report->save();

        return response()->json([
            'success' => true,
            'report_type' => $type,
            'report' => $report
        ]);
    }
}
