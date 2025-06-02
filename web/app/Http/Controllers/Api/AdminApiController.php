<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UnifiedUser;
use Illuminate\Http\Request;
use App\Services\UploadService;
use Illuminate\Support\Facades\Storage;

class AdminApiController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    // List all admins
    public function index(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $admins = UnifiedUser::where('role_id', 1)->get();
        return response()->json([
            'success' => true,
            'admins' => $admins->map(function ($admin) {
                return [
                    'id' => $admin->id,
                    'first_name' => $admin->first_name,
                    'last_name' => $admin->last_name,
                    'email' => $admin->email,
                    'personal_email' => $admin->personal_email,
                    'mobile' => $admin->mobile,
                    'photo' => $admin->photo,
                    'photo_url' => $admin->photo
                        ? Storage::disk('spaces-private')->temporaryUrl($admin->photo, now()->addMinutes(30))
                        : null,
                    // Add other fields as needed
                ];
            })
        ]);
    }

    // Show a single admin
    public function show(Request $request, $id)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $admin = UnifiedUser::where('role_id', 1)->findOrFail($id);
        return response()->json([
            'success' => true,
            'admin' => [
                'id' => $admin->id,
                'first_name' => $admin->first_name,
                'last_name' => $admin->last_name,
                'email' => $admin->email,
                'personal_email' => $admin->personal_email,
                'mobile' => $admin->mobile,
                'photo' => $admin->photo,
                'photo_url' => $admin->photo
                    ? Storage::disk('spaces-private')->temporaryUrl($admin->photo, now()->addMinutes(30))
                    : null,
                // Add other fields as needed
            ]
        ]);
    }

    // Store a new admin
    public function store(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validator = \Validator::make($request->all(), [
            'first_name' => [
                'required',
                'string',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/',
                'max:100'
            ],
            'last_name' => [
                'required',
                'string',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/',
                'max:100'
            ],
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(),
            'gender' => 'nullable|string|in:Male,Female,Other',
            'civil_status' => 'nullable|string|in:Single,Married,Widowed,Divorced',
            'religion' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/', 
            ],
            'nationality' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/', 
            ],
            'educational_background' => 'nullable|string|in:College,Highschool,Doctorate',
            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.-]+$/',
            ],
            'account.email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:cose_users,email',
            ],
            'personal_email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:cose_users,personal_email',
            ],
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/',
                'unique:cose_users,mobile',
            ],
            'landline_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]{7,10}$/',
            ],
            'account.password' => 'required|string|min:8|confirmed',
            'Organization_Roles' => [
                'required',
                'integer',
                'exists:organization_roles,organization_role_id',
                function ($attribute, $value, $fail) {
                    if ($value == 1) {
                        $existingExecutiveDirector = \App\Models\User::where('organization_role_id', 1)->exists();
                        if ($existingExecutiveDirector) {
                            $fail('There can only be one Executive Director. Please select a different role.');
                        }
                    }
                },
            ],
            'administrator_photo' => 'nullable|image|mimes:jpeg,png|max:7168',
            'government_ID' => 'nullable|image|mimes:jpeg,png|max:7168',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:5120',
            'sss_ID' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10}$/',
            ],
            'philhealth_ID' => [
                'nullable',
                'string',
                'regex:/^[0-9]{12}$/',
            ],
            'pagibig_ID' => [
                'nullable',
                'string',
                'regex:/^[0-9]{12}$/',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $uniqueIdentifier = time() . '_' . \Illuminate\Support\Str::random(5);

            $administratorPhotoPath = null;
            if ($request->hasFile('administrator_photo')) {
                $administratorPhotoPath = $this->uploadService->upload(
                    $request->file('administrator_photo'),
                    'spaces-private',
                    'uploads/administrator_photos',
                    $firstName . '_' . $lastName . '_photo_' . $uniqueIdentifier . '.' . $request->file('administrator_photo')->getClientOriginalExtension()
                );
            }

            $governmentIDPath = null;
            if ($request->hasFile('government_ID')) {
                $governmentIDPath = $this->uploadService->upload(
                    $request->file('government_ID'),
                    'spaces-private',
                    'uploads/administrator_government_ids',
                    $firstName . '_' . $lastName . '_government_id_' . $uniqueIdentifier . '.' . $request->file('government_ID')->getClientOriginalExtension()
                );
            }

            $resumePath = null;
            if ($request->hasFile('resume')) {
                $resumePath = $this->uploadService->upload(
                    $request->file('resume'),
                    'spaces-private',
                    'uploads/administrator_resumes',
                    $firstName . '_' . $lastName . '_resume_' . $uniqueIdentifier . '.' . $request->file('resume')->getClientOriginalExtension()
                );
            }

            $administrator = new \App\Models\User();
            $administrator->first_name = $request->input('first_name');
            $administrator->last_name = $request->input('last_name');
            $administrator->birthday = $request->input('birth_date');
            $administrator->gender = $request->input('gender') ?? null;
            $administrator->civil_status = $request->input('civil_status') ?? null;
            $administrator->religion = $request->input('religion') ?? null;
            $administrator->nationality = $request->input('nationality') ?? null;
            $administrator->educational_background = $request->input('educational_background') ?? null;
            $administrator->address = $request->input('address_details');
            $administrator->email = $request->input('account.email');
            $administrator->personal_email = $request->input('personal_email');
            $administrator->mobile = '+63' . $request->input('mobile_number');
            $administrator->landline = $request->input('landline_number') ?? null;
            $administrator->password = bcrypt($request->input('account.password'));
            $administrator->organization_role_id = $request->input('Organization_Roles');
            $administrator->role_id = 1;
            $administrator->volunteer_status = 'Active';
            $administrator->status = 'Active';
            $administrator->status_start_date = now();
            $administrator->photo = $administratorPhotoPath;
            $administrator->government_issued_id = $governmentIDPath;
            $administrator->cv_resume = $resumePath;
            $administrator->sss_id_number = $request->input('sss_ID') ?? null;
            $administrator->philhealth_id_number = $request->input('philhealth_ID') ?? null;
            $administrator->pagibig_id_number = $request->input('pagibig_ID') ?? null;
            $administrator->remember_token = \Illuminate\Support\Str::random(60);

            $administrator->save();

            return response()->json([
                'success' => true,
                'administrator' => [
                    'id' => $administrator->id,
                    'first_name' => $administrator->first_name,
                    'last_name' => $administrator->last_name,
                    'email' => $administrator->email,
                    'personal_email' => $administrator->personal_email,
                    'mobile' => $administrator->mobile,
                    'photo' => $administrator->photo,
                    'photo_url' => $administrator->photo
                        ? Storage::disk('spaces-private')->temporaryUrl($administrator->photo, now()->addMinutes(30))
                        : null,
                    // Add other fields as needed
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Update an admin
    public function update(Request $request, $id)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $administrator = \App\Models\User::where('role_id', 1)->findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'first_name' => [
                'required',
                'string',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/',
                'max:100'
            ],
            'last_name' => [
                'required',
                'string',
                'regex:/^[A-ZÑ][a-zA-ZÑñ\'\.\s\-]*$/',
                'max:100'
            ],
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(),
            'gender' => 'nullable|string|in:Male,Female,Other',
            'civil_status' => 'nullable|string|in:Single,Married,Widowed,Divorced',
            'religion' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/', 
            ],
            'nationality' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/', 
            ],
            'educational_background' => 'nullable|string|in:College,Highschool,Doctorate',
            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.-]+$/',
            ],
            'account.email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:cose_users,email,' . $administrator->id,
            ],
            'personal_email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:cose_users,personal_email,' . $administrator->id,
            ],
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/',
                'unique:cose_users,mobile,' . $administrator->id,
            ],
            'landline_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]{7,10}$/',
            ],
            'account.password' => 'nullable|string|min:8|confirmed',
            'Organization_Roles' => [
                'required',
                'integer',
                'exists:organization_roles,organization_role_id',
                function ($attribute, $value, $fail) use ($administrator) {
                    if ($value == 1 && $administrator->organization_role_id != 1) {
                        $existingExecutiveDirector = \App\Models\User::where('organization_role_id', 1)
                            ->where('id', '!=', $administrator->id)
                            ->exists();
                        if ($existingExecutiveDirector) {
                            $fail('There can only be one Executive Director. Please select a different role.');
                        }
                    }
                },
            ],
            'administrator_photo' => 'nullable|image|mimes:jpeg,png|max:7168',
            'government_ID' => 'nullable|image|mimes:jpeg,png|max:7168',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:5120',
            'sss_ID' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10}$/',
            ],
            'philhealth_ID' => [
                'nullable',
                'string',
                'regex:/^[0-9]{12}$/',
            ],
            'pagibig_ID' => [
                'nullable',
                'string',
                'regex:/^[0-9]{12}$/',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $uniqueIdentifier = time() . '_' . \Illuminate\Support\Str::random(5);

            if ($request->hasFile('administrator_photo')) {
                if ($administrator->photo) {
                    $this->uploadService->delete($administrator->photo, 'spaces-private');
                }
                $administrator->photo = $this->uploadService->upload(
                    $request->file('administrator_photo'),
                    'spaces-private',
                    'uploads/administrator_photos',
                    $firstName . '_' . $lastName . '_photo_' . $uniqueIdentifier . '.' . $request->file('administrator_photo')->getClientOriginalExtension()
                );
            }
            if ($request->hasFile('government_ID')) {
                if ($administrator->government_issued_id) {
                    $this->uploadService->delete($administrator->government_issued_id, 'spaces-private');
                }
                $administrator->government_issued_id = $this->uploadService->upload(
                    $request->file('government_ID'),
                    'spaces-private',
                    'uploads/administrator_government_ids',
                    $firstName . '_' . $lastName . '_government_id_' . $uniqueIdentifier . '.' . $request->file('government_ID')->getClientOriginalExtension()
                );
            }
            if ($request->hasFile('resume')) {
                if ($administrator->cv_resume) {
                    $this->uploadService->delete($administrator->cv_resume, 'spaces-private');
                }
                $administrator->cv_resume = $this->uploadService->upload(
                    $request->file('resume'),
                    'spaces-private',
                    'uploads/administrator_resumes',
                    $firstName . '_' . $lastName . '_resume_' . $uniqueIdentifier . '.' . $request->file('resume')->getClientOriginalExtension()
                );
            }

            $administrator->first_name = $request->input('first_name');
            $administrator->last_name = $request->input('last_name');
            $administrator->birthday = $request->input('birth_date');
            $administrator->gender = $request->input('gender') ?? null;
            $administrator->civil_status = $request->input('civil_status') ?? null;
            $administrator->religion = $request->input('religion') ?? null;
            $administrator->nationality = $request->input('nationality') ?? null;
            $administrator->educational_background = $request->input('educational_background') ?? null;
            $administrator->address = $request->input('address_details');
            $administrator->email = $request->input('account.email');
            $administrator->personal_email = $request->input('personal_email');
            $administrator->mobile = '+63' . $request->input('mobile_number');
            $administrator->landline = $request->input('landline_number') ?? null;
            if ($request->filled('account.password')) {
                $administrator->password = bcrypt($request->input('account.password'));
            }
            $administrator->organization_role_id = $request->input('Organization_Roles');
            $administrator->sss_id_number = $request->input('sss_ID') ?? null;
            $administrator->philhealth_id_number = $request->input('philhealth_ID') ?? null;
            $administrator->pagibig_id_number = $request->input('pagibig_ID') ?? null;

            $administrator->save();

            return response()->json([
                'success' => true,
                'administrator' => [
                    'id' => $administrator->id,
                    'first_name' => $administrator->first_name,
                    'last_name' => $administrator->last_name,
                    'email' => $administrator->email,
                    'personal_email' => $administrator->personal_email,
                    'mobile' => $administrator->mobile,
                    'photo' => $administrator->photo,
                    'photo_url' => $administrator->photo
                        ? Storage::disk('spaces-private')->temporaryUrl($administrator->photo, now()->addMinutes(30))
                        : null,
                    // Add other fields as needed
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Delete an admin (soft delete)
    public function destroy(Request $request, $id)
    {
        // Only allow Admin to delete admins
        if ($request->user()->role_id !== 1) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $administrator = \App\Models\User::where('role_id', 1)->findOrFail($id);

        // Prevent deleting self
        if ($administrator->id === $request->user()->id) {
            return response()->json(['error' => 'You cannot delete your own account.'], 403);
        }

        // (Optional) Check for dependencies, e.g., audit logs, assignments, etc.
        // if ($administrator->hasDependencies()) {
        //     return response()->json(['error' => 'Cannot delete admin with dependencies.'], 400);
        // }

        try {
            $administrator->delete();

            // Log the deletion
            // $this->logService->createLog(...);

            // Send notifications
            // $this->sendNotificationToAdmin(...);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Change the status of an admin (Active/Inactive).
     */
    public function changeStatus(Request $request, $id)
    {
        // Only allow Admin to change status
        if ($request->user()->role_id !== 1) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validator = \Validator::make($request->all(), [
            'status' => 'required|string|in:Active,Inactive'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $administrator = \App\Models\User::where('role_id', 1)->findOrFail($id);

        // Prevent changing own status to Inactive
        if ($administrator->id === $request->user()->id && $request->status === 'Inactive') {
            return response()->json(['error' => 'You cannot deactivate your own account.'], 403);
        }

        $administrator->status = $request->status;
        $administrator->status_start_date = now();
        $administrator->save();

        // Log the status change
        // $this->logService->createLog(...);

        // Send notifications
        // $this->sendNotificationToAdmin(...);

        return response()->json([
            'success' => true,
            'administrator' => $administrator
        ]);
    }

    /**
     * Restore a soft-deleted admin.
     */
    public function restore(Request $request, $id)
    {
        // Only allow Admin to restore admins
        if ($request->user()->role_id !== 1) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $administrator = \App\Models\User::withTrashed()->where('role_id', 1)->findOrFail($id);

        if (!$administrator->trashed()) {
            return response()->json(['error' => 'Admin is not deleted.'], 400);
        }

        try {
            $administrator->restore();

            // Log the restore
            // $this->logService->createLog(...);

            // Send notifications
            // $this->sendNotificationToAdmin(...);

            return response()->json([
                'success' => true,
                'administrator' => $administrator
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
