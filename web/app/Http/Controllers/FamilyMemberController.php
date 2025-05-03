<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\FamilyMember;
// use App\Models\Beneficiary;
// use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Municipality;
use App\Models\FamilyMember;
use App\Models\Beneficiary;
use App\Models\GeneralCarePlan;
use App\Models\Notification;
use App\Services\UserManagementService;

class FamilyMemberController extends Controller
{
    protected $userManagementService;
    
    public function __construct(UserManagementService $userManagementService)
    {
        $this->userManagementService = $userManagementService;
    }

    protected function getRolePrefix()
    {
        $user = Auth::user();
        
        // Match the role_id checking logic used in CheckRole middleware
        if ($user->role_id == 1) {
            return 'admin';
        } elseif ($user->role_id == 2) {
            return 'careManager';
        } elseif ($user->role_id == 3) {
            return 'careWorker';
        }
        
        return 'admin'; // Default fallback
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');
        $rolePrefix = $this->getRolePrefix();

        // For care workers, only show family members related to their assigned beneficiaries
        if (Auth::user()->role_id == 3) {
            // First retrieve beneficiaries assigned to this care worker directly
            $assignedBeneficiaryIds = Beneficiary::whereHas('generalCarePlan', function($query) {
                $query->where('care_worker_id', Auth::id());
            })->pluck('beneficiary_id')->toArray();
            
            // Debug log to check if we're getting any beneficiaries
            \Log::debug('Care worker assigned beneficiaries: ' . json_encode($assignedBeneficiaryIds));
            
            // Then find family members related to those beneficiaries
            $family_members = FamilyMember::with(['beneficiary.municipality'])
                ->whereIn('related_beneficiary_id', $assignedBeneficiaryIds)
                ->when($search, function ($query, $search) {
                    return $query->where(function($q) use ($search) {
                        $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
                })
                ->when($filter, function ($query, $filter) {
                    if ($filter == 'access') {
                        return $query->orderBy('access');
                    }
                })
                ->orderBy('first_name')
                ->get();
            } else {
                // Admin and care manager code remains unchanged
                $family_members = FamilyMember::with(['beneficiary.municipality'])
                    ->when($search, function ($query, $search) {
                        return $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                                ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
                    })
                    ->when($filter, function ($query, $filter) {
                        if ($filter == 'access') {
                            return $query->orderBy('access');
                        }
                    })
                    ->orderBy('first_name')
                    ->get();
            }
        
        $family_members->map(function ($family_member) {
            $family_member->status = $family_member->access ? 'Approved' : 'Denied';
            $family_member->municipality = $family_member->beneficiary->municipality;
            return $family_member;
        });

        // Determine which view to return based on user role
        $viewName = $rolePrefix . '.familyProfile';
        
        return view($viewName, compact('family_members', 'search', 'filter'));
    }

    public function viewFamilyDetails(Request $request)
    {
        $rolePrefix = $this->getRolePrefix();

        // Get role-specific redirect
        if (Auth::user()->role_id == 1) { // Admin
            $rolePrefixRoute = 'admin';
        } elseif (Auth::user()->role_id == 2) { // Care Manager
            $rolePrefixRoute = 'care-manager';
        } else { // Care Worker
            $rolePrefixRoute = 'care-worker';
        } 

        $family_member_id = $request->input('family_member_id');
        $family_member = FamilyMember::with('beneficiary')->find($family_member_id);

        if (!$family_member) {
            return redirect()->route($rolePrefixRoute . '.families.index')->with('error', 'Family member not found.');
        }

        // For care workers, check if they are assigned to this beneficiary
        if (Auth::user()->role_id == 3) {
            // Remove toArray() - keep it as a collection so contains() works
            $assignedBeneficiaryIds = Beneficiary::whereHas('generalCarePlan', function($query) {
                $query->where('care_worker_id', Auth::id());
            })->pluck('beneficiary_id'); // Removed toArray() here
            
            if (!$assignedBeneficiaryIds->contains($family_member->related_beneficiary_id)) {
                return redirect()->route($rolePrefixRoute . '.families.index')
                    ->with('error', 'You do not have permission to view this family member.');
            }
        }

        // Add the status property based on the access value
        $family_member->status = $family_member->access ? 'Approved' : 'Denied';

        return view($rolePrefix . '.viewFamilyDetails', compact('family_member'));
    }
    
    // To revise so that dropdown will be dynamic
    public function create()
    {
        $rolePrefix = $this->getRolePrefix();
        
        // For care workers, only show their assigned beneficiaries
        if (Auth::user()->role_id == 3) {
            $beneficiaries = Beneficiary::whereHas('generalCarePlan', function($query) {
                $query->where('care_worker_id', Auth::id());
            })
            ->select('beneficiary_id', 'first_name', 'last_name')
            ->get();
        } else {
            // For admin and care manager, show all beneficiaries
            $beneficiaries = Beneficiary::select('beneficiary_id', 'first_name', 'last_name')->get();
        }
        
        return view($rolePrefix . '.addFamily', compact('beneficiaries'));
    }

    public function storeFamily(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            // Personal Details
            'family_photo' => 'nullable|image|mimes:jpeg,png|max:2048',
            'first_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'gender' => 'nullable|string|in:Male,Female,Other', // Must match dropdown options
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(), // Must be older than 14 years
            
            'relatedBeneficiary' => 'required|integer|exists:beneficiaries,beneficiary_id',
            'relation_to_beneficiary' => [
                'required',
                'string',
                'in:Son,Daughter,Spouse,Sibling,Grandchild,Other',
            ],

            'personal_email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:cose_users,personal_email',
            ],

            'is_primary_caregiver' => [
                'required',
                'boolean',
            ],

            // Address
            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.-]+$/', // Allows alphanumeric characters, spaces, commas, periods, and hyphens
            ],
        
            // Contact Information
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/', //  10 or 11 digits, +63 preceeding
                function ($attribute, $value, $fail) {
                    // Check uniqueness in family_members table with the +63 prefix
                    $exists = FamilyMember::where('mobile', '+63'.$value)->exists();
                    if ($exists) {
                        $fail('The mobile number has already been taken.');
                    }
                }
            ],
            'landline_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]{7,10}$/', // Between 7 and 10 digits
            ],
        
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $uniqueIdentifier = Str::random(10);

        try {
            $uniqueIdentifier = Str::random(10);
    
            // Store profile picture if it exists
            if ($request->hasFile('family_photo')) {
                $familyPhotoPath = $request->file('family_photo')->storeAs(
                    'uploads/family_photos', 
                    $request->input('first_name') . '_' . $request->input('last_name') . '_family_member_photo_' . $uniqueIdentifier . '.' . $request->file('family_photo')->getClientOriginalExtension(),
                    'public'
                );
            }

        // Retrieve the portal_account_id from the selected beneficiary
        $beneficiary = Beneficiary::find($request->input('relatedBeneficiary'));
        if (!$beneficiary) {
            return redirect()->back()->withErrors(['relatedBeneficiary' => 'The selected beneficiary does not exist.'])->withInput();
        }

        // Check if another family member is already the primary caregiver for the same beneficiary
        if ($request->input('is_primary_caregiver')) {
            $existingPrimaryCaregiver = FamilyMember::where('related_beneficiary_id', $request->input('relatedBeneficiary'))
                ->where('is_primary_caregiver', true)
                ->first();

            if ($existingPrimaryCaregiver) {
                return redirect()->back()->withErrors([
                    'is_primary_caregiver' => 'There is already a primary caregiver for this beneficiary. Please update the existing caregiver or set this family member as not the primary caregiver.'
                ])->withInput();
            }

            // Check if the beneficiary's primary_caregiver is null or not tied to a family member
            if (is_null($beneficiary->primary_caregiver) || !FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)
                ->where('is_primary_caregiver', true)
                ->exists()) {
                // Update the beneficiary's primary_caregiver field
                $beneficiary->primary_caregiver = $request->input('first_name') . ' ' . $request->input('last_name');
                $beneficiary->save();
            }
        }

        // Save the administrator to the database
        $familymember = new FamilyMember();
        $familymember->first_name = $request->input('first_name');
        $familymember->last_name = $request->input('last_name');
        // $familymember->name = $request->input('name') . ' ' . $request->input('last_name'); // Combine first and last name
        $familymember->gender = $request->input('gender') ?? null;
        $familymember->birthday = $request->input('birth_date');
        $familymember->mobile = '+63' . $request->input('mobile_number');
        $familymember->landline = $request->input('landline_number') ?? null;
        $familymember->is_primary_caregiver = $request->input('is_primary_caregiver');
        $familymember->related_beneficiary_id = $request->input('relatedBeneficiary');
        $familymember->relation_to_beneficiary = $request->input('relation_to_beneficiary');
        $familymember->email = $request->input('personal_email');
        // $familymember->password = bcrypt($request->input('account.password'));
        $familymember->street_address = $request->input('address_details');
        // $familymember->access = True; // Status for access to the system (REMOVED BECAUSE 'access' COLUMN WILL BE REMOVED)
        $familymember->created_at = now();
        $familymember->created_by = Auth::id(); // Set the created_by column to the current user's ID   
        $familymember->updated_by = Auth::id(); // Set the updated_by column to the current user's ID
        // $familymember->assigned_municipality_id = $request->input('municipality');

        // Save file paths and IDs
        $familymember->photo = $familyPhotoPath ?? null;

        // Assign the portal_account_id from the beneficiary
        $familymember->portal_account_id = $beneficiary->portal_account_id;

        // Generate and save the remember_token
        $familymember->remember_token = Str::random(60);


        $familymember->save();

        try {
            // Get the role-specific name
            $actorRole = Auth::user()->role_id == 1 ? 'Administrator' : (Auth::user()->role_id == 2 ? 'Care Manager' : 'Care Worker');
            $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            
            // 1. Send welcome notification to the family member (if they have a portal account)
            if ($familymember->portal_account_id) {
                $welcomeTitle = 'Welcome to SULONG KALINGA';
                $welcomeMessage = 'Welcome ' . $familymember->first_name . ' ' . $familymember->last_name . 
                                '! Your family member account has been added to SULONG KALINGA system.';
                $this->sendNotificationToFamilyMember($familymember->family_member_id, $welcomeTitle, $welcomeMessage);
            }
            
            // 2. Notify the connected beneficiary
            $beneficiary = Beneficiary::find($familymember->related_beneficiary_id);
            if ($beneficiary) {
                $beneficiaryTitle = 'New Family Member Connected';
                $beneficiaryMessage = $familymember->first_name . ' ' . $familymember->last_name . 
                                    ' has been added as your ' . strtolower($familymember->relation_to_beneficiary) . 
                                    ' in the system by ' . $actor . ' (' . $actorRole . ').';
                $this->sendNotificationToBeneficiary($beneficiary->beneficiary_id, $beneficiaryTitle, $beneficiaryMessage);
                
                // 3. Get care worker assigned to the beneficiary (if any)
                $careWorker = null;
                $careWorkerId = null;
                
                if ($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id) {
                    $careWorkerId = $beneficiary->generalCarePlan->care_worker_id;
                    
                    // Only notify if care worker is not the one who added the family member
                    if ($careWorkerId && $careWorkerId != Auth::id()) {
                        $careWorkerTitle = 'New Family Member for Your Beneficiary';
                        $careWorkerMessage = $familymember->first_name . ' ' . $familymember->last_name . 
                                          ' has been added as a ' . strtolower($familymember->relation_to_beneficiary) . 
                                          ' for your beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name . 
                                          ' by ' . $actor . ' (' . $actorRole . ').';
                        $this->sendNotificationToCareWorker($careWorkerId, $careWorkerTitle, $careWorkerMessage);
                        
                        // 4. Get care manager assigned to the care worker (if any)
                        $careWorker = User::find($careWorkerId);
                        if ($careWorker && $careWorker->assigned_care_manager_id && $careWorker->assigned_care_manager_id != Auth::id()) {
                            $careManagerTitle = 'New Family Member for Beneficiary';
                            $careManagerMessage = $familymember->first_name . ' ' . $familymember->last_name . 
                                               ' has been added as a ' . strtolower($familymember->relation_to_beneficiary) . 
                                               ' for beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name . 
                                               ', who is assigned to your care worker ' . $careWorker->first_name . ' ' . $careWorker->last_name . 
                                               ' by ' . $actor . ' (' . $actorRole . ').';
                            $this->sendNotificationToCareManager($careWorker->assigned_care_manager_id, $careManagerTitle, $careManagerMessage);
                        }
                    }
                }
            }
        } catch (\Exception $notifyEx) {
            // Log notification errors but don't interrupt the main process
            \Log::warning('Error sending notifications for new family member: ' . $notifyEx->getMessage());
        }

        // Get role-specific redirect
        if (Auth::user()->role_id == 1) { // Admin
            $redirectRoute = 'admin.families.create';
        } elseif (Auth::user()->role_id == 2) { // Care Manager
            $redirectRoute = 'care-manager.families.create';
        } else { // Care Worker
            $redirectRoute = 'care-worker.families.create';
        } 
        
        // Redirect with success message
        return redirect()->route($redirectRoute)->with('success', 'Family Member or Relative has been successfully added!');
    
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a unique constraint violation
            if ($e->getCode() == 23505) { // PostgreSQL unique violation error code
                // Check which field caused the violation
                if (strpos($e->getMessage(), 'mobile') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['mobile_number' => 'This mobile number is already registered in the system.']);
                } elseif (strpos($e->getMessage(), 'cose_users_personal_email_unique') !== false || 
                        strpos($e->getMessage(), 'email') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['personal_email' => 'This email address is already registered in the system.']);
                } else {
                    // Generic unique constraint error
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['error' => 'A record with some of this information already exists.']);
                }
            }

            // For other database errors
            \Log::error('Database error when creating family member: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while saving the family member. Please try again.']);
        } catch (\Exception $e) {
            // For any other unexpected errors
            \Log::error('Unexpected error when creating family member: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    public function editFamilyMember($id)
    {
        $rolePrefix = $this->getRolePrefix();
        
        // Get the family member with their related beneficiary
        $familyMember = FamilyMember::findOrFail($id);
        
        // For care workers, check if they are assigned to this beneficiary
        if (Auth::user()->role_id == 3) {
            // Replace the assignedBeneficiaries() call with direct query
            $assignedBeneficiaryIds = Beneficiary::whereHas('generalCarePlan', function($query) {
                $query->where('care_worker_id', Auth::id());
            })->pluck('beneficiary_id');
            
            if (!$assignedBeneficiaryIds->contains($familyMember->related_beneficiary_id)) {
                return redirect()->route($rolePrefix . '.families.index')
                    ->with('error', 'You do not have permission to edit this family member.');
            }
            
            // Also fix the beneficiaries query
            $beneficiaries = Beneficiary::whereHas('generalCarePlan', function($query) {
                $query->where('care_worker_id', Auth::id());
            })
            ->select('beneficiary_id', 'first_name', 'last_name')
            ->get();
        } else {
            // For admin and care manager, show all beneficiaries
            $beneficiaries = Beneficiary::select('beneficiary_id', 'first_name', 'last_name')->get();
        }
        
        // Return the view with the data
        return view($rolePrefix . '.editFamilyProfile', compact('familyMember', 'beneficiaries'));
    }

    public function updateFamilyMember(Request $request, $id)
    {
        $rolePrefix = $this->getRolePrefix();

        // Validate the input data - similar to your store method but with some modifications
        $validator = Validator::make($request->all(), [
            'family_photo' => 'nullable|image|mimes:jpeg,png|max:2048',
            'first_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'gender' => 'nullable|string|in:Male,Female,Other',
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(),
            'relatedBeneficiary' => 'required|integer|exists:beneficiaries,beneficiary_id',
            'relation_to_beneficiary' => [
                'required',
                'string',
                'in:Son,Daughter,Spouse,Sibling,Grandchild,Other',
            ],
            'personal_email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('family_members', 'email')->ignore($id, 'family_member_id')
            ],
            'is_primary_caregiver' => [
                'required',
                'boolean',
            ],
            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.-]+$/',
            ],
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/',
                function ($attribute, $value, $fail) use ($id) {
                    // Check uniqueness in family_members table with the +63 prefix
                    // Exclude the current record being updated
                    $exists = FamilyMember::where('mobile', '+63'.$value)
                                          ->where('family_member_id', '!=', $id)
                                          ->exists();
                    if ($exists) {
                        $fail('The mobile number has already been taken.');
                    }
                },
            ],
            'landline_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]{7,10}$/',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Find the existing family member
            $familyMember = FamilyMember::findOrFail($id);
            // For care workers, check if they are assigned to this beneficiary
            
            // Store original details for comparison
            $originalBeneficiaryId = $familyMember->related_beneficiary_id;

            if (Auth::user()->role_id == 3) {
                // Replace the assignedBeneficiaries() call with direct query
                $assignedBeneficiaryIds = Beneficiary::whereHas('generalCarePlan', function($query) {
                    $query->where('care_worker_id', Auth::id());
                })->pluck('beneficiary_id');
                
                if (!$assignedBeneficiaryIds->contains($familyMember->related_beneficiary_id)) {
                    return redirect()->route($rolePrefix . '.families.index')
                        ->with('error', 'You do not have permission to update this family member.');
                }
            }
            
            $uniqueIdentifier = Str::random(10);

            // Handle the photo upload if a new one was provided
            if ($request->hasFile('family_photo')) {
                // If there's an existing photo, delete it
                if ($familyMember->photo && file_exists(public_path('storage/' . $familyMember->photo))) {
                    unlink(public_path('storage/' . $familyMember->photo));
                }
                
                $familyPhotoPath = $request->file('family_photo')->storeAs(
                    'uploads/family_photos',
                    $request->input('first_name') . '_' . $request->input('last_name') . '_family_member_photo_' . $uniqueIdentifier . '.' . $request->file('family_photo')->getClientOriginalExtension(),
                    'public'
                );
                
                $familyMember->photo = $familyPhotoPath;
            }

            // Get the beneficiary
            $beneficiary = Beneficiary::find($request->input('relatedBeneficiary'));
            if (!$beneficiary) {
                return redirect()->back()->withErrors(['relatedBeneficiary' => 'The selected beneficiary does not exist.'])->withInput();
            }

            // Handle primary caregiver logic
            if ($request->input('is_primary_caregiver')) {
                // Check for existing primary caregiver other than this one
                $existingPrimaryCaregiver = FamilyMember::where('related_beneficiary_id', $request->input('relatedBeneficiary'))
                    ->where('is_primary_caregiver', true)
                    ->where('family_member_id', '!=', $id) // Exclude the current member
                    ->first();

                if ($existingPrimaryCaregiver) {
                    return redirect()->back()->withErrors([
                        'is_primary_caregiver' => 'There is already a primary caregiver for this beneficiary.'
                    ])->withInput();
                }

                // Update the beneficiary's primary_caregiver field
                $beneficiary->primary_caregiver = $request->input('first_name') . ' ' . $request->input('last_name');
                $beneficiary->save();
            }
            // If was primary and now isn't, update beneficiary record
            elseif ($familyMember->is_primary_caregiver && !$request->input('is_primary_caregiver')) {
                if ($beneficiary->primary_caregiver == $familyMember->first_name . ' ' . $familyMember->last_name) {
                    $beneficiary->primary_caregiver = null;
                    $beneficiary->save();
                }
            }

            // Update the family member data
            $familyMember->first_name = $request->input('first_name');
            $familyMember->last_name = $request->input('last_name');
            $familyMember->gender = $request->input('gender') ?? null;
            $familyMember->birthday = $request->input('birth_date');
            $familyMember->mobile = '+63' . $request->input('mobile_number');
            $familyMember->landline = $request->input('landline_number') ?? null;
            $familyMember->is_primary_caregiver = $request->input('is_primary_caregiver') ? true : false;
            $familyMember->related_beneficiary_id = $request->input('relatedBeneficiary');
            $familyMember->relation_to_beneficiary = $request->input('relation_to_beneficiary');
            $familyMember->email = $request->input('personal_email');
            $familyMember->street_address = $request->input('address_details');
            $familyMember->updated_by = Auth::id();
            $familyMember->portal_account_id = $beneficiary->portal_account_id;

            $familyMember->save();

            try {
                // Get the role-specific name
                $actorRole = Auth::user()->role_id == 1 ? 'Administrator' : (Auth::user()->role_id == 2 ? 'Care Manager' : 'Care Worker');
                $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                
                // 1. Notify the family member about the update
                if ($familyMember->portal_account_id) {
                    $updateTitle = 'Your Profile Was Updated';
                    $updateMessage = 'Your family member profile information has been updated by ' . $actor . ' (' . $actorRole . ').';
                    $this->sendNotificationToFamilyMember($familyMember->family_member_id, $updateTitle, $updateMessage);
                }
                
                // 2. Check if related beneficiary changed
                if ($originalBeneficiaryId != $familyMember->related_beneficiary_id) {
                    // Notify old beneficiary if applicable
                    $oldBeneficiary = Beneficiary::find($originalBeneficiaryId);
                    if ($oldBeneficiary && $oldBeneficiary->portal_account_id) {
                        $oldBeneficiaryTitle = 'Family Member Unlinked';
                        $oldBeneficiaryMessage = $familyMember->first_name . ' ' . $familyMember->last_name . 
                                              ' is no longer linked to your profile by ' . $actor . ' (' . $actorRole . ').';
                        $this->sendNotificationToBeneficiary($oldBeneficiary->beneficiary_id, $oldBeneficiaryTitle, $oldBeneficiaryMessage);
                    }
                    
                    // Notify new beneficiary
                    $newBeneficiary = Beneficiary::find($familyMember->related_beneficiary_id);
                    if ($newBeneficiary && $newBeneficiary->portal_account_id) {
                        $newBeneficiaryTitle = 'New Family Member Linked';
                        $newBeneficiaryMessage = $familyMember->first_name . ' ' . $familyMember->last_name . 
                                              ' has been linked to your profile as your ' . strtolower($familyMember->relation_to_beneficiary) . 
                                              ' by ' . $actor . ' (' . $actorRole . ').';
                        $this->sendNotificationToBeneficiary($newBeneficiary->beneficiary_id, $newBeneficiaryTitle, $newBeneficiaryMessage);
                    }
                } else {
                    // Notify current beneficiary about the family member update
                    $beneficiary = Beneficiary::find($familyMember->related_beneficiary_id);
                    if ($beneficiary && $beneficiary->portal_account_id) {
                        $beneficiaryTitle = 'Family Member Profile Updated';
                        $beneficiaryMessage = 'Your ' . strtolower($familyMember->relation_to_beneficiary) . ', ' . 
                                             $familyMember->first_name . ' ' . $familyMember->last_name . 
                                             ', has had their profile information updated by ' . $actor . ' (' . $actorRole . ').';
                        $this->sendNotificationToBeneficiary($beneficiary->beneficiary_id, $beneficiaryTitle, $beneficiaryMessage);
                    }
                }
            } catch (\Exception $notifyEx) {
                // Log notification errors but don't interrupt the main process
                \Log::warning('Error sending notifications for family member update: ' . $notifyEx->getMessage());
            }

            // Redirect with success message
            if (Auth::user()->role_id == 1) { // Admin
                return redirect()->route('admin.families.index')
                ->with('success', 'Family Member ' . $familyMember->first_name . ' ' . $familyMember->last_name . ' updated successfully!');
            } elseif (Auth::user()->role_id == 2) { // Care Manager
                return redirect()->route('care-manager.families.index')
                ->with('success', 'Family Member ' . $familyMember->first_name . ' ' . $familyMember->last_name . ' updated successfully!');
            } else { // Care Worker
                return redirect()->route('care-worker.families.index')
                ->with('success', 'Family Member ' . $familyMember->first_name . ' ' . $familyMember->last_name . ' updated successfully!');
            }
        
        } catch (\Exception $e) {
            \Log::error('Error updating family member: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function deleteFamilyMember(Request $request)
    {
        // Only admins and care managers can delete
        if (Auth::user()->role_id == 3) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete family members.'
            ]);
        }
        
        try {
            $family_member_id = $request->input('family_member_id');
            
            // Retrieve the family member and associated beneficiary before deletion
            $familyMember = FamilyMember::with('beneficiary')->find($family_member_id);
            
            if (!$familyMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'Family member not found.'
                ]);
            }
            
            // Store details for notifications
            $familyMemberName = $familyMember->first_name . ' ' . $familyMember->last_name;
            $relationshipType = $familyMember->relation_to_beneficiary;
            $beneficiaryId = $familyMember->related_beneficiary_id;
            $beneficiary = $familyMember->beneficiary;
            
            // Check if the family member has acknowledged any weekly care plans
            $hasAcknowledgedPlans = \DB::table('weekly_care_plans')
                ->where('acknowledged_by_family', $family_member_id)
                ->exists();
            
            if ($hasAcknowledgedPlans) {
                return response()->json([
                    'success' => false,
                    'message' => 'This family member cannot be deleted because they have acknowledged weekly care plans. For data integrity, please retain this record.',
                    'error_type' => 'dependency_care_plans'
                ]);
            }
            
            try {
                // Get role and name for notification messages
                $actorRole = Auth::user()->role_id == 1 ? 'Administrator' : 'Care Manager';
                $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                
                // 1. Notify the connected beneficiary about the deletion
                if ($beneficiary && $beneficiary->portal_account_id) {
                    $beneficiaryTitle = 'Family Member Removed';
                    $beneficiaryMessage = 'Your ' . strtolower($relationshipType) . ', ' . $familyMemberName . 
                                    ', has been removed from the system by ' . $actor . ' (' . $actorRole . ').';
                    $this->sendNotificationToBeneficiary($beneficiaryId, $beneficiaryTitle, $beneficiaryMessage);
                }
                
                // 2. Check if there's a care worker assigned to the beneficiary
                $careWorkerId = null;
                $careWorker = null;
                
                if ($beneficiary && $beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id) {
                    $careWorkerId = $beneficiary->generalCarePlan->care_worker_id;
                    $careWorker = User::find($careWorkerId);
                    
                    // Only notify care worker if they aren't the one deleting
                    if ($careWorkerId && $careWorkerId != Auth::id()) {
                        $careWorkerTitle = 'Family Member Deleted for Your Beneficiary';
                        $careWorkerMessage = $familyMemberName . ', the ' . strtolower($relationshipType) . 
                                        ' of your beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name . 
                                        ', has been removed from the system by ' . $actor . ' (' . $actorRole . ').';
                        $this->sendNotificationToCareWorker($careWorkerId, $careWorkerTitle, $careWorkerMessage);
                        
                        // 3. Notify care manager if there is one assigned to the care worker
                        if ($careWorker && $careWorker->assigned_care_manager_id && $careWorker->assigned_care_manager_id != Auth::id()) {
                            $careManagerTitle = 'Family Member Deleted';
                            $careManagerMessage = $familyMemberName . ', the ' . strtolower($relationshipType) . 
                                            ' of beneficiary ' . $beneficiary->first_name . ' ' . $beneficiary->last_name . 
                                            ' (assigned to your care worker ' . $careWorker->first_name . ' ' . $careWorker->last_name . 
                                            '), has been removed from the system by ' . $actor . ' (' . $actorRole . ').';
                            $this->sendNotificationToCareManager($careWorker->assigned_care_manager_id, $careManagerTitle, $careManagerMessage);
                        }
                    }
                }
            } catch (\Exception $notifyEx) {
                // Log notification errors but continue with deletion
                \Log::warning('Failed to send family member deletion notifications: ' . $notifyEx->getMessage());
            }
            
            // If no acknowledgments found, proceed with deletion
            $result = $this->userManagementService->deleteFamilyMember(
                $family_member_id,
                Auth::user()
            );
            
            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Error during family member deletion: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'family_member_id' => $request->input('family_member_id')
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while deleting the family member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to a family member
     *
     * @param int $familyMemberId ID of the family member to notify
     * @param string $title Notification title  
     * @param string $message Notification message
     * @return void
     */
    private function sendNotificationToFamilyMember($familyMemberId, $title, $message)
    {
        try {
            // Get family member to retrieve their portal account ID
            $familyMember = FamilyMember::find($familyMemberId);
            if (!$familyMember || !$familyMember->portal_account_id) {
                \Log::warning('Cannot send notification to family member: No portal account found for ID ' . $familyMemberId);
                return;
            }
            
            // Create notification
            $notification = new Notification();
            $notification->user_id = $familyMember->portal_account_id;
            $notification->user_type = 'family_member';
            $notification->message_title = $title;
            $notification->message = $message;
            $notification->date_created = now();
            $notification->is_read = false;
            $notification->save();
            
            \Log::info('Created notification for family member ' . $familyMemberId);
        } catch (\Exception $e) {
            \Log::error('Failed to send notification to family member ' . $familyMemberId . ': ' . $e->getMessage());
        }
    }

    /**
     * Send notification to a beneficiary
     *
     * @param int $beneficiaryId ID of the beneficiary to notify
     * @param string $title Notification title  
     * @param string $message Notification message
     * @return void
     */
    private function sendNotificationToBeneficiary($beneficiaryId, $title, $message)
    {
        try {
            // Get beneficiary to retrieve their portal account ID
            $beneficiary = Beneficiary::find($beneficiaryId);
            if (!$beneficiary || !$beneficiary->portal_account_id) {
                \Log::warning('Cannot send notification to beneficiary: No portal account found for ID ' . $beneficiaryId);
                return;
            }
            
            // Create notification
            $notification = new Notification();
            $notification->user_id = $beneficiary->portal_account_id;
            $notification->user_type = 'beneficiary';
            $notification->message_title = $title;
            $notification->message = $message;
            $notification->date_created = now();
            $notification->is_read = false;
            $notification->save();
            
            \Log::info('Created notification for beneficiary ' . $beneficiaryId);
        } catch (\Exception $e) {
            \Log::error('Failed to send notification to beneficiary ' . $beneficiaryId . ': ' . $e->getMessage());
        }
    }

    /**
     * Send notification to a care worker
     *
     * @param int $careWorkerId ID of the care worker to notify
     * @param string $title Notification title  
     * @param string $message Notification message
     * @return void
     */
    private function sendNotificationToCareWorker($careWorkerId, $title, $message)
    {
        try {
            // Ensure care worker exists and is active
            $careWorker = User::where('id', $careWorkerId)
                ->where('role_id', 3)
                ->where('status', 'Active')
                ->first();
                
            if (!$careWorker) {
                \Log::warning('Cannot send notification: No active care worker found with ID ' . $careWorkerId);
                return;
            }
            
            // Create notification
            $notification = new Notification();
            $notification->user_id = $careWorkerId;
            $notification->user_type = 'cose_staff';
            $notification->message_title = $title;
            $notification->message = $message;
            $notification->date_created = now();
            $notification->is_read = false;
            $notification->save();
            
            \Log::info('Created notification for care worker ' . $careWorkerId);
        } catch (\Exception $e) {
            \Log::error('Failed to send notification to care worker ' . $careWorkerId . ': ' . $e->getMessage());
        }
    }

    /**
     * Send notification to a care manager
     *
     * @param int $careManagerId ID of the care manager to notify
     * @param string $title Notification title  
     * @param string $message Notification message
     * @return void
     */
    private function sendNotificationToCareManager($careManagerId, $title, $message)
    {
        try {
            // Ensure care manager exists and is active
            $careManager = User::where('id', $careManagerId)
                ->where('role_id', 2)
                ->where('status', 'Active')
                ->first();
                
            if (!$careManager) {
                \Log::warning('Cannot send notification: No active care manager found with ID ' . $careManagerId);
                return;
            }
            
            // Create notification
            $notification = new Notification();
            $notification->user_id = $careManagerId;
            $notification->user_type = 'cose_staff';
            $notification->message_title = $title;
            $notification->message = $message;
            $notification->date_created = now();
            $notification->is_read = false;
            $notification->save();
            
            \Log::info('Created notification for care manager ' . $careManagerId);
        } catch (\Exception $e) {
            \Log::error('Failed to send notification to care manager ' . $careManagerId . ': ' . $e->getMessage());
        }
    }

}