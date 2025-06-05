<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\UploadService;
use Illuminate\Support\Facades\Storage;

class FamilyMemberApiController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    // List all family members
    public function index(Request $request)
    {
        $query = FamilyMember::with('beneficiary');

        // Optional: search/filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'ILIKE', "%{$search}%")
                  ->orWhere('last_name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        $familyMembers = $query->orderBy('first_name')->get();

        return response()->json([
            'success' => true,
            'family_members' => $familyMembers->map(function($fm) {
                return [
                    'family_member_id' => $fm->family_member_id,
                    'first_name' => $fm->first_name,
                    'last_name' => $fm->last_name,
                    'gender' => $fm->gender,
                    'birth_date' => $fm->birthday,
                    'email' => $fm->email,
                    'mobile_number' => $fm->mobile,
                    'landline_number' => $fm->landline,
                    'relation_to_beneficiary' => $fm->relation_to_beneficiary,
                    'is_primary_caregiver' => $fm->is_primary_caregiver,
                    'address_details' => $fm->street_address,
                    'photo' => $fm->photo,
                    'photo_url' => $fm->photo
                        ? $this->uploadService->getTemporaryPrivateUrl($fm->photo, 30)
                        : null,
                    'beneficiary' => $fm->beneficiary,
                ];
            })
        ]);
    }

    // Show a single family member
    public function show($id)
    {
        $user = request()->user();

        $familyMember = FamilyMember::with(['beneficiary.municipality'])->findOrFail($id);

        // --- Care worker access control (only assigned beneficiaries) ---
        if ($user && $user->role_id == 3) {
            $assignedBeneficiaryIds = \App\Models\Beneficiary::whereHas('generalCarePlan', function($query) use ($user) {
                $query->where('care_worker_id', $user->id);
            })->pluck('beneficiary_id');
            if (!$assignedBeneficiaryIds->contains($familyMember->related_beneficiary_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view this family member.'
                ], 403);
            }
        }

        // --- Logging (view action) ---
        if (class_exists('\App\Services\LogService')) {
            try {
                $logService = app(\App\Services\LogService::class);
                $logService->createLog(
                    'family_member',
                    $familyMember->family_member_id,
                    \App\Enums\LogType::VIEW,
                    ($user ? $user->first_name . ' ' . $user->last_name : 'Unknown user') . ' viewed family member ' . $familyMember->first_name . ' ' . $familyMember->last_name,
                    $user ? $user->id : null
                );
            } catch (\Throwable $e) {
                \Log::warning('Failed to log family member view: ' . $e->getMessage());
            }
        }

        // Compute status based on access (if field exists)
        $status = null;
        if (isset($familyMember->access)) {
            $status = $familyMember->access == 1 ? 'Approved' : 'Denied';
        }

        // Add municipality info if available
        $municipality = $familyMember->beneficiary && $familyMember->beneficiary->municipality
            ? [
                'municipality_id' => $familyMember->beneficiary->municipality->municipality_id,
                'municipality_name' => $familyMember->beneficiary->municipality->municipality_name,
            ]
            : null;

        return response()->json([
            'success' => true,
            'data' => [
                'family_member_id' => $familyMember->family_member_id,
                'first_name' => $familyMember->first_name,
                'last_name' => $familyMember->last_name,
                'gender' => $familyMember->gender,
                'birth_date' => $familyMember->birthday,
                'email' => $familyMember->email,
                'mobile_number' => $familyMember->mobile,
                'landline_number' => $familyMember->landline,
                'relation_to_beneficiary' => $familyMember->relation_to_beneficiary,
                'is_primary_caregiver' => $familyMember->is_primary_caregiver,
                'address_details' => $familyMember->street_address,
                'photo' => $familyMember->photo,
                'photo_url' => $familyMember->photo
                    ? $this->uploadService->getTemporaryPrivateUrl($familyMember->photo, 30)
                    : null,
                'beneficiary' => $familyMember->beneficiary,
                'municipality' => $municipality,
                'access' => $familyMember->access ?? null,
                'status' => $status,
            ]
        ]);
    }

    // Add a new family member (admin only)
    public function store(Request $request)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(),
            'email' => [
                'required', 'email',
                Rule::unique('family_members', 'email'),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],
            'mobile_number' => [
                'required', 'string',
                Rule::unique('family_members', 'mobile'),
            ],
            'landline_number' => 'nullable|string',
            'related_beneficiary_id' => 'required|integer|exists:beneficiaries,beneficiary_id',
            'relation_to_beneficiary' => 'required|string|max:50',
            'photo' => 'sometimes|nullable|image|max:2048',
            'address_details' => 'required|string',
            'is_primary_caregiver' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('is_primary_caregiver') && $request->is_primary_caregiver) {
            $exists = FamilyMember::where('related_beneficiary_id', $request->related_beneficiary_id)
                ->where('is_primary_caregiver', true)
                ->exists();
            if ($exists) {
                return response()->json([
                    'errors' => [
                        'is_primary_caregiver' => ['There is already a primary caregiver for this beneficiary.']
                    ]
                ], 422);
            }
        }

        $mobile = $request->mobile_number;
        if (preg_match('/^09\d{9}$/', $mobile)) {
            $mobile = preg_replace('/^0/', '+63', $mobile);
        }

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'birthday' => $request->birth_date,
            'email' => $request->email,
            'mobile' => $mobile,
            'landline' => $request->landline_number,
            'relation_to_beneficiary' => $request->relation_to_beneficiary,
            'is_primary_caregiver' => $request->is_primary_caregiver ?? false,
            'street_address' => $request->address_details,
            'related_beneficiary_id' => $request->related_beneficiary_id,
            'password' => bcrypt($request->password),
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $uniqueIdentifier = time() . '_' . \Illuminate\Support\Str::random(5);
            $data['photo'] = $this->uploadService->upload(
                $request->file('photo'),
                'spaces-private',
                'uploads/family_member_photos',
                [
                    'filename' => $request->input('first_name') . '_' . $request->input('last_name') . '_photo_' . $uniqueIdentifier . '.' . $request->file('photo')->getClientOriginalExtension()
                ]
            );
        }

        $familyMember = FamilyMember::create($data);

        return response()->json([
            'success' => true,
            'data' => [
                'family_member_id' => $familyMember->family_member_id,
                'first_name' => $familyMember->first_name,
                'last_name' => $familyMember->last_name,
                'gender' => $familyMember->gender,
                'birth_date' => $familyMember->birthday,
                'email' => $familyMember->email,
                'mobile_number' => $familyMember->mobile,
                'landline_number' => $familyMember->landline,
                'relation_to_beneficiary' => $familyMember->relation_to_beneficiary,
                'is_primary_caregiver' => $familyMember->is_primary_caregiver,
                'address_details' => $familyMember->street_address,
                'photo' => $familyMember->photo,
                'photo_url' => $familyMember->photo
                    ? $this->uploadService->getTemporaryPrivateUrl($familyMember->photo, 30)
                    : null,
                'beneficiary' => $familyMember->beneficiary,
            ]
        ]);
    }

    // Edit family member (admin only)
    public function update(Request $request, $id)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $familyMember = FamilyMember::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'gender' => 'sometimes|nullable|string|in:Male,Female,Other',
            'birth_date' => 'sometimes|required|date|before_or_equal:' . now()->subYears(14)->toDateString(),
            'email' => [
                'sometimes', 'required', 'email',
                Rule::unique('family_members', 'email')->ignore($familyMember->family_member_id, 'family_member_id'),
            ],
            'password' => [
                'sometimes',
                'nullable',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],
            'mobile_number' => [
                'sometimes', 'required', 'string',
                Rule::unique('family_members', 'mobile')->ignore($familyMember->family_member_id, 'family_member_id'),
            ],
            'landline_number' => 'sometimes|nullable|string',
            'related_beneficiary_id' => 'sometimes|required|integer|exists:beneficiaries,beneficiary_id',
            'relation_to_beneficiary' => 'sometimes|required|string|max:50',
            'photo' => 'sometimes|nullable|image|max:2048',
            'address_details' => 'sometimes|required|string',
            'is_primary_caregiver' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('is_primary_caregiver') && $request->is_primary_caregiver) {
            $exists = FamilyMember::where('related_beneficiary_id', $request->related_beneficiary_id ?? $familyMember->related_beneficiary_id)
                ->where('is_primary_caregiver', true)
                ->where('family_member_id', '!=', $familyMember->family_member_id)
                ->exists();
            if ($exists) {
                return response()->json([
                    'errors' => [
                        'is_primary_caregiver' => ['There is already a primary caregiver for this beneficiary.']
                    ]
                ], 422);
            }
        }

        if ($request->has('mobile_number')) {
            $mobile = $request->mobile_number;
            if (preg_match('/^09\d{9}$/', $mobile)) {
                $mobile = preg_replace('/^0/', '+63', $mobile);
            }
            $request->merge(['mobile_number' => $mobile]);
        }

        $data = [
            'updated_by' => $request->user()->id,
        ];
        foreach (['first_name', 'last_name', 'gender', 'birth_date', 'email', 'mobile_number', 'landline_number', 'relation_to_beneficiary', 'is_primary_caregiver', 'address_details', 'related_beneficiary_id'] as $field) {
            if ($request->has($field)) {
                if ($field === 'birth_date') {
                    $data['birthday'] = $request->birth_date;
                } elseif ($field === 'mobile_number') {
                    $data['mobile'] = $request->mobile_number;
                } elseif ($field === 'landline_number') {
                    $data['landline'] = $request->landline_number;
                } elseif ($field === 'address_details') {
                    $data['street_address'] = $request->address_details;
                } else {
                    $data[$field] = $request->$field;
                }
            }
        }

        // Handle password update
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // Handle photo upload (delete old photo if new one is uploaded)
        if ($request->hasFile('photo')) {
            if ($familyMember->photo) {
                $this->uploadService->delete($familyMember->photo, 'spaces-private');
            }
            $uniqueIdentifier = time() . '_' . \Illuminate\Support\Str::random(5);
            $data['photo'] = $this->uploadService->upload(
                $request->file('photo'),
                'spaces-private',
                'uploads/family_member_photos',
                [
                    'filename' => $familyMember->first_name . '_' . $familyMember->last_name . '_photo_' . $uniqueIdentifier . '.' .
                    $request->file('photo')->getClientOriginalExtension()
                ]
            );
        }

        $familyMember->update($data);

        // Handle primary caregiver updates
        if ($request->has('is_primary_caregiver')) {
            $beneficiary = \App\Models\Beneficiary::find($request->related_beneficiary_id ?? $familyMember->related_beneficiary_id);
            if ($request->is_primary_caregiver) {
                $beneficiary->primary_caregiver = $familyMember->family_member_id;
            } elseif ($familyMember->is_primary_caregiver && !$request->is_primary_caregiver) {
                $beneficiary->primary_caregiver = null;
            }
            $beneficiary->save();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'family_member_id' => $familyMember->family_member_id,
                'first_name' => $familyMember->first_name,
                'last_name' => $familyMember->last_name,
                'gender' => $familyMember->gender,
                'birth_date' => $familyMember->birthday,
                'email' => $familyMember->email,
                'mobile_number' => $familyMember->mobile,
                'landline_number' => $familyMember->landline,
                'relation_to_beneficiary' => $familyMember->relation_to_beneficiary,
                'is_primary_caregiver' => $familyMember->is_primary_caregiver,
                'address_details' => $familyMember->street_address,
                'photo' => $familyMember->photo,
                'photo_url' => $familyMember->photo
                    ? $this->uploadService->getTemporaryPrivateUrl($familyMember->photo, 30)
                    : null,
                'beneficiary' => $familyMember->beneficiary,
            ]
        ]);
    }

    // Delete family member (admin only)
    public function destroy(Request $request, $id)
    {
        if (!in_array($request->user()->role_id, [1, 2])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $familyMember = FamilyMember::findOrFail($id);

        // Check for acknowledged care plans
        if (method_exists($familyMember, 'acknowledgedCarePlans') && $familyMember->acknowledgedCarePlans()->exists()) {
            return response()->json([
                'errors' => [
                    'delete' => ['Cannot delete because this family member has acknowledged care plans in weekly_care_plans.']
                ]
            ], 422);
        }

        // Check if this family member is set as primary_caregiver in beneficiaries
        $isPrimaryInBeneficiary = \App\Models\Beneficiary::where('primary_caregiver', $familyMember->family_member_id)->exists();
        if ($isPrimaryInBeneficiary) {
            return response()->json([
                'errors' => [
                    'delete' => ['Cannot delete because this family member is set as the primary caregiver for a beneficiary.']
                ]
            ], 422);
        }

        // Attempt delete and catch foreign key constraint errors
        try {
            // Delete photo from storage
            if ($familyMember->photo) {
                $this->uploadService->delete($familyMember->photo, 'spaces-private');
            }

            $familyMember->delete();

            return response()->json(['success' => true]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for foreign key violation
            if ($e->getCode() === '23503') {
                return response()->json([
                    'errors' => [
                        'delete' => ['Cannot delete because this family member is still referenced in other records (e.g., weekly_care_plans or other related tables).']
                    ]
                ], 422);
            }
            // Other database errors
            return response()->json([
                'errors' => [
                    'delete' => ['Delete failed due to a database error: ' . $e->getMessage()]
                ]
            ], 500);
        }
    }

}