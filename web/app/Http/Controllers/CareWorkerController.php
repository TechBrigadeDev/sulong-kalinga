<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Municipality;
use App\Models\GeneralCarePlan;
use App\Models\Notification;
use App\Models\Beneficiary;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Services\UserManagementService;
 

class CareWorkerController extends Controller
{

    protected $userManagementService;
    
    public function __construct(UserManagementService $userManagementService)
    {
        $this->userManagementService = $userManagementService;
    }

    protected function getRolePrefixView()
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

    protected function getRolePrefixRoute()
    {

        $user = Auth::user();
        
        // Match the role_id checking logic used in CheckRole middleware
        if ($user->role_id == 1) {
            return 'admin';
        } elseif ($user->role_id == 2) {
            return 'care-manager';
        } elseif ($user->role_id == 3) {
            return 'care-worker';
        }
        
        return 'admin'; // Default fallback
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');
        $rolePrefix = $this->getRolePrefixView();

        // Fetch careworkers based on the search query and filters
        $careworkers = User::where('role_id', 3)
            ->with(['municipality', 'assignedCareManager']) // Add the relationship
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            })
            ->when($filter, function ($query, $filter) {
                if ($filter == 'status') {
                    return $query->orderBy('volunteer_status');
                } elseif ($filter == 'municipality') {
                    return $query->orderBy('assigned_municipality_id');
                }
            })
            ->orderBy('first_name')
            ->get();

        // Get all care managers for filtering/assignment dropdowns
        $careManagers = User::where('role_id', 2)
                            ->where('status', 'Active')
                            ->orderBy('first_name')
                            ->get();

        // Pass the data to the Blade template
        return view($rolePrefix . '.careWorkerProfile', compact('careworkers', 'careManagers'));
    }

    public function viewCareworkerDetails(Request $request)
    {
        $rolePrefix = $this->getRolePrefixView();
        $careworker_id = $request->input('careworker_id');
        $careworker = User::where('role_id', 3)
        ->with('municipality')->find($careworker_id);

        if (!$careworker) {
            return redirect()->route($rolePrefix . '.careworkers.index')->with('error', 'Care worker not found.');
        }

        // Fetch all general care plans associated with this care worker
        $generalCarePlans = GeneralCarePlan::where('care_worker_id', $careworker_id)->get();

        // Fetch all beneficiaries associated with these general care plans
        $beneficiaries = Beneficiary::whereIn('general_care_plan_id', $generalCarePlans->pluck('general_care_plan_id'))->get();

        return view($rolePrefix . '.viewCareworkerDetails', compact('careworker', 'beneficiaries'));
    }

    public function editCareworkerProfile($id)
    {
        $rolePrefix = $this->getRolePrefixView();
        $careworker = User::where('role_id', 3)->findOrFail($id);

        // Fetch all municipalities for the dropdown
        $municipalities = Municipality::all();

        // Fetch all active care managers (role_id = 2)
        $careManagers = User::where('role_id', 2)
        ->where('status', 'Active')
        ->orderBy('first_name')
        ->get();

        // Format date for the form
        $birth_date = null;
        if ($careworker->birthday) {
            $birth_date = Carbon::parse($careworker->birthday)->format('Y-m-d');
        }

        // Pass data to the view using role-specific path
        return view($rolePrefix . '.editCareworkerProfile', compact('careworker', 'municipalities', 'careManagers', 'birth_date'));
    }

    public function updateCareWorker(Request $request, $id)
    {
        $rolePrefix = $this->getRolePrefixRoute();

        // Find the care worker by ID
        $careworker = User::where('role_id', 3)->findOrFail($id);

        // Store original care manager ID to detect changes
        $originalCareManagerId = $careworker->assigned_care_manager_id;

        // Validate the request data
        $validator = Validator::make($request->all(), [
            // Personal Details
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
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(),
            'gender' => 'nullable|string|in:Male,Female,Other',
            'civil_status' => 'nullable|string|in:Single,Married,Widowed,Divorced',
            'religion' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'nationality' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'educational_background' => 'nullable|string|in:College,Highschool,Doctorate',
        
            // Address
            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.-]+$/',
            ],
        
            // Email fields - with unique constraint exceptions for this user
            'account.email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:cose_users,email,' . $id,
            ],
            'personal_email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:cose_users,personal_email,' . $id,
            ],
            
            // Contact Information
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/',
                'unique:cose_users,mobile,' . $id,
            ],
            'landline_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]{7,10}$/',
            ],
        
            // Password is optional for updates
            'account.password' => 'nullable|string|min:8|confirmed',
        
            // Municipality
            'municipality' => 'required|integer|exists:municipalities,municipality_id',
        
            // Documents - optional for updates
            'careworker_photo' => 'nullable|image|mimes:jpeg,png|max:2048',
            'government_ID' => 'nullable|image|mimes:jpeg,png|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048',
        
            // IDs
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

            'assigned_care_manager' => 'nullable|exists:cose_users,id,role_id,2',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Handle file uploads if any
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $uniqueIdentifier = time() . '_' . Str::random(5);

            // Process new photo if uploaded
            if ($request->hasFile('careworker_photo')) {
                $careworkerPhotoPath = $request->file('careworker_photo')->storeAs(
                    'uploads/careworker_photos', 
                    $firstName . '' . $lastName . '_photo' . $uniqueIdentifier . '.' . $request->file('careworker_photo')->getClientOriginalExtension(),
                    'public'
                );
                $careworker->photo = $careworkerPhotoPath;
            } else {
                // If no new photo is uploaded, keep the existing one
                if ($careworker->photo) {
                    $careworkerPhotoPath = $careworker->photo;
                } else {
                    $careworkerPhotoPath = null; // No photo available
                }
            }

            // Process new government ID if uploaded
            if ($request->hasFile('government_ID')) {
                $governmentIDPath = $request->file('government_ID')->storeAs(
                    'uploads/careworker_government_ids', 
                    $firstName . '' . $lastName . '_government_id' . $uniqueIdentifier . '.' . $request->file('government_ID')->getClientOriginalExtension(),
                    'public'
                );
                $careworker->government_issued_id = $governmentIDPath;
            }

            // Process new resume if uploaded
            if ($request->hasFile('resume')) {
                $resumePath = $request->file('resume')->storeAs(
                    'uploads/careworker_resumes', 
                    $firstName . '' . $lastName . '_resume' . $uniqueIdentifier . '.' . $request->file('resume')->getClientOriginalExtension(),
                    'public'
                );
                $careworker->cv_resume = $resumePath;
            }

            // Update careworker details
            $careworker->first_name = $request->input('first_name');
            $careworker->last_name = $request->input('last_name');
            $careworker->birthday = $request->input('birth_date');
            $careworker->gender = $request->input('gender');
            $careworker->civil_status = $request->input('civil_status');
            $careworker->religion = $request->input('religion');
            $careworker->nationality = $request->input('nationality');
            $careworker->educational_background = $request->input('educational_background');
            $careworker->address = $request->input('address_details');
            $careworker->email = $request->input('account.email');
            $careworker->personal_email = $request->input('personal_email');
            $careworker->mobile = '+63' . $request->input('mobile_number');
            $careworker->landline = $request->input('landline_number');
            
            // Update password only if provided
            if ($request->filled('account.password')) {
                $careworker->password = bcrypt($request->input('account.password'));
            }
            
            $careworker->assigned_municipality_id = $request->input('municipality');
            $careworker->assigned_care_manager_id = $request->input('assigned_care_manager');
            $careworker->sss_id_number = $request->input('sss_ID') === '' ? null : $request->input('sss_ID');
            $careworker->philhealth_id_number = $request->input('philhealth_ID') === '' ? null : $request->input('philhealth_ID');
            $careworker->pagibig_id_number = $request->input('pagibig_ID') === '' ? null : $request->input('pagibig_ID');
            
            $careworker->save();

            // Only notify if the user is not updating their own profile
            if (Auth::id() != $careworker->id) {
                try {
                    // Send notification to the care worker whose details were updated
                    $title = 'Your Profile Was Updated';
                    $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                    $message = 'Your profile was updated by ' . $actor . '.';
                    $this->sendNotificationToCareWorker($careworker->id, $title, $message);
                    
                    // Check if care manager assignment changed
                    if ($originalCareManagerId != $careworker->assigned_care_manager_id) {
                        // Notify the old care manager (if there was one and it's not the current user)
                        if ($originalCareManagerId && $originalCareManagerId != Auth::id()) {
                            $oldManagerTitle = 'Care Worker Reassigned';
                            $oldManagerMessage = 'Care worker ' . $careworker->first_name . ' ' . $careworker->last_name . 
                                            ' has been reassigned from you by ' . $actor . '.';
                            $this->sendNotificationToCareManager($originalCareManagerId, $oldManagerTitle, $oldManagerMessage);
                        }
                        
                        // Notify the new care manager (if there is one and it's not the current user)
                        if ($careworker->assigned_care_manager_id && $careworker->assigned_care_manager_id != Auth::id()) {
                            $newManagerTitle = 'New Care Worker Assigned';
                            $newManagerMessage = 'Care worker ' . $careworker->first_name . ' ' . $careworker->last_name . 
                                            ' has been assigned to you by ' . $actor . '.';
                            $this->sendNotificationToCareManager($careworker->assigned_care_manager_id, $newManagerTitle, $newManagerMessage);
                        }
                        
                        // Also notify the care worker about the care manager change
                        $workerTitle = 'Care Manager Assignment Changed';
                        if ($careworker->assigned_care_manager_id) {
                            // Get the new care manager's name
                            $newCareManager = User::find($careworker->assigned_care_manager_id);
                            $workerMessage = 'You have been assigned to a new care manager: ' . 
                                        $newCareManager->first_name . ' ' . $newCareManager->last_name . '.';
                        } else {
                            $workerMessage = 'You are no longer assigned to a care manager.';
                        }
                        $this->sendNotificationToCareWorker($careworker->id, $workerTitle, $workerMessage);
                    }
                } catch (\Exception $notifyEx) {
                    // Log but continue - don't let notification failure prevent update
                    \Log::warning('Failed to send profile update notification to care worker: ' . $notifyEx->getMessage());
                }
            }

            // Redirect with success message
            return redirect()->route($rolePrefix . '.careworkers.index')
                ->with('success', 'Care Worker ' . $careworker->first_name . ' ' . $careworker->last_name . ' has been successfully updated!');
                
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors
            \Log::error('Database error when updating care worker: ' . $e->getMessage());
            
            // Check if it's a unique constraint violation
            if ($e->getCode() == 23505) { // PostgreSQL unique violation error code
                // Check which field caused the violation
                if (strpos($e->getMessage(), 'cose_users_mobile_unique') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['mobile_number' => 'This mobile number is already registered with another user.']);
                } elseif (strpos($e->getMessage(), 'cose_users_email_unique') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['account.email' => 'This email address is already registered with another user.']);
                } elseif (strpos($e->getMessage(), 'cose_users_personal_email_unique') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['personal_email' => 'This personal email address is already registered with another user.']);
                }
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while updating the care worker. Please try again.']);
        } catch (\Exception $e) {
            // Handle other unexpected errors
            \Log::error('Unexpected error when updating care worker: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    // To revise so that dropdown will be dynamic
    public function create()
    {
        $rolePrefix = $this->getRolePrefixView();
        
        // Fetch all municipalities from the database
        $municipalities = Municipality::all();
        
        // Fetch all active care managers (role_id = 2)
        $careManagers = User::where('role_id', 2)
                            ->where('status', 'Active')
                            ->orderBy('first_name')
                            ->get();

        // Pass the data to the view
        return view($rolePrefix . '.addCareWorker', compact('municipalities', 'careManagers'));
    }

    public function storeCareWorker(Request $request)
    {
        $rolePrefix = $this->getRolePrefixRoute();

        // Validate the input data
        $validator = Validator::make($request->all(), [
            // Personal Details
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
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(), // Must be older than 14 years
            'gender' => 'nullable|string|in:Male,Female,Other', // Must match dropdown options
            'civil_status' => 'nullable|string|in:Single,Married,Widowed,Divorced', // Must match dropdown options
            'religion' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'nationality' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/'
            ],
            'educational_background' => 'nullable|string|in:College,Highschool,Doctorate', // Must match dropdown options
        
            // Address
            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.-]+$/', // Allows alphanumeric characters, spaces, commas, periods, and hyphens
            ],
        
            
            // Email fields
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
            // Contact Information
            'mobile_number' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/', //  10 or 11 digits, +63 preceeding
                'unique:cose_users,mobile',
            ],
            'landline_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]{7,10}$/', // Between 7 and 10 digits
            ],
        
            // Account Registration
            'account.password' => 'required|string|min:8|confirmed',
        
            // // Organization Roles
            // 'Organization_Roles' => 'required|integer|exists:organization_roles,organization_role_id',
            
            // Municipality
            'municipality' => 'required|integer|exists:municipalities,municipality_id',
        
            // Documents
            'careworker_photo' => 'nullable|image|mimes:jpeg,png|max:2048',
            'government_ID' => 'nullable|image|mimes:jpeg,png|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048',
        
            // IDs
            'sss_ID' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10}$/', // 10 digits
            ],
            'philhealth_ID' => [
                'nullable',
                'string',
                'regex:/^[0-9]{12}$/', // 12 digits
            ],
            'pagibig_ID' => [
                'nullable',
                'string',
                'regex:/^[0-9]{12}$/', // 12 digits
            ],

            'assigned_care_manager' => 'nullable|exists:cose_users,id,role_id,2',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Handle file uploads and rename files - existing code
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $uniqueIdentifier = time() . '_' . Str::random(5);

            if ($request->hasFile('careworker_photo')) {
                $administratorPhotoPath = $request->file('careworker_photo')->storeAs(
                    'uploads/careworker_photos', 
                    $firstName . '' . $lastName . '_photo' . $uniqueIdentifier . '.' . $request->file('careworker_photo')->getClientOriginalExtension(),
                    'public'
                );
            }
    
            if ($request->hasFile('government_ID')) {
                $governmentIDPath = $request->file('government_ID')->storeAs(
                    'uploads/careworker_government_ids', 
                    $firstName . '' . $lastName . '_government_id' . $uniqueIdentifier . '.' . $request->file('government_ID')->getClientOriginalExtension(),
                    'public'
                );
            }
    
            if ($request->hasFile('resume')) {
                $resumePath = $request->file('resume')->storeAs(
                    'uploads/careworker_resumes', 
                    $firstName . '' . $lastName . '_resume' . $uniqueIdentifier . '.' . $request->file('resume')->getClientOriginalExtension(),
                    'public'
                );
            }

        // Save the administrator to the database
        $careworker = new User();

        //All other fields
        $careworker->first_name = $request->input('first_name');
        $careworker->last_name = $request->input('last_name');
        // $careworker->name = $request->input('name') . ' ' . $request->input('last_name'); // Combine first and last name
        $careworker->birthday = $request->input('birth_date');
        $careworker->gender = $request->input('gender') ?? null;
        $careworker->civil_status = $request->input('civil_status') ?? null;
        $careworker->religion = $request->input('religion') ?? null;
        $careworker->nationality = $request->input('nationality') ?? null;
        $careworker->educational_background = $request->input('educational_background') ?? null;
        $careworker->address = $request->input('address_details');
        $careworker->email = $request->input('account.email'); // Work email
        $careworker->personal_email = $request->input('personal_email'); // Personal email
        $careworker->mobile = '+63' . $request->input('mobile_number');
        $careworker->landline = $request->input('landline_number') ?? null;
        $careworker->password = bcrypt($request->input('account.password'));
        // $careworker->organization_role_id = $request->input('Organization_Roles');
        $careworker->role_id = 3; // 3 is the role ID for care workers
        $careworker->volunteer_status = 'Active'; // Status in COSE
        $careworker->status = 'Active'; // Status for access to the system
        $careworker->status_start_date = now();
        $careworker->assigned_municipality_id = $request->input('municipality');
        $careworker->assigned_care_manager_id = $request->input('assigned_care_manager');

        // Save file paths and IDs
        $careworker->photo = $careworkerPhotoPath ?? null;
        $careworker->government_issued_id = $request->hasFile('government_ID') ? $governmentIDPath : null;
        $careworker->cv_resume = $request->hasFile('resume') ? $resumePath : null;
        $careworker->sss_id_number = $request->input('sss_ID') ?? null;
        $careworker->philhealth_id_number = $request->input('philhealth_ID') ?? null;
        $careworker->pagibig_id_number = $request->input('pagibig_ID') ?? null;

        // Generate and save the remember_token
        $careworker->remember_token = Str::random(60);


        $careworker->save();

         // Send welcome notification to the new care worker
         try {
            $welcomeTitle = 'Welcome to SULONG KALINGA';
            $welcomeMessage = 'Welcome ' . $careworker->first_name . ' ' . $careworker->last_name . 
                             '! Your care worker account has been created. You can now access the system with your credentials.';
            $this->sendNotificationToCareWorker($careworker->id, $welcomeTitle, $welcomeMessage);
            
            // If assigned to a care manager, notify them about the new care worker
            if ($careworker->assigned_care_manager_id) {
                // Only notify if the care manager isn't the one who created the care worker
                if ($careworker->assigned_care_manager_id != Auth::id()) {
                    $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                    $title = 'New Care Worker Assigned';
                    $message = $actor . ' has assigned a new care worker, ' . $careworker->first_name . ' ' . 
                              $careworker->last_name . ', to you.';
                    $this->sendNotificationToCareManager($careworker->assigned_care_manager_id, $title, $message);
                }
            }
        } catch (\Exception $notifyEx) {
            // Log but continue - don't let notification failure prevent account creation
            \Log::warning('Failed to send welcome notification to new care worker: ' . $notifyEx->getMessage());
        }

        // Redirect with success message
        return redirect()->route($rolePrefix . '.careworkers.create')
            ->with('success', 'Care worker added successfully.');
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a unique constraint violation
            if ($e->getCode() == 23505) { // PostgreSQL unique violation error code
                // Check which field caused the violation
                if (strpos($e->getMessage(), 'cose_users_mobile_unique') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['mobile_number' => 'This mobile number is already registered in the system.']);
                } elseif (strpos($e->getMessage(), 'cose_users_email_unique') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['account.email' => 'This email address is already registered in the system.']);
                } elseif (strpos($e->getMessage(), 'cose_users_personal_email_unique') !== false) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['personal_email' => 'This personal email address is already registered in the system.']);
                } else {
                    // Generic unique constraint error
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['error' => 'A record with some of this information already exists.']);
                }
            }

            // For other database errors
            \Log::error('Database error when creating care worker: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while saving the care worker. Please try again.']);
        } catch (\Exception $e) {
            // For any other unexpected errors
            \Log::error('Unexpected error when creating care worker: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    public function updateStatusAjax($id, Request $request)
    {
        \Log::info('Update care worker status AJAX request', [
            'care_worker_id' => $id,
            'status' => $request->input('status')
        ]);
        
        try {
            // Find care worker (role_id = 3)
            $careWorker = User::where('role_id', 3)->find($id);
            
            if (!$careWorker) {
                return response()->json(['success' => false, 'message' => 'Care worker not found.'], 404);
            }
            
            // Don't allow updating your own status
            if ($careWorker->id == Auth::id()) {
                return response()->json(['success' => false, 'message' => 'You cannot change your own status.'], 400);
            }
            
            // Get the status directly
            $status = $request->input('status');
            $oldStatus = $careWorker->status;
            
            // Update ONLY the status column
            $careWorker->status = $status;
            $careWorker->updated_at = now();
            $careWorker->save();
            
            // Attempt to send notifications, but don't let failures break the main functionality
            try {
                $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                
                // Only send notifications if care worker is active (to avoid sending to inactive users)
                if ($status === 'Active') {
                    // Send notification to the care worker whose status was changed
                    $title = ($oldStatus === 'Inactive') 
                        ? 'Welcome Back to SULONG KALINGA' 
                        : 'Your Account Status Changed';
                        
                    $message = ($oldStatus === 'Inactive')
                        ? 'Welcome back, ' . $careWorker->first_name . '! Your account has been reactivated by ' . $actor . '.'
                        : 'Your account status was changed from ' . $oldStatus . ' to ' . $status . ' by ' . $actor . '.';
                    
                    $notification = new Notification();
                    $notification->user_id = $careWorker->id;
                    $notification->user_type = 'cose_staff';
                    $notification->message_title = $title;
                    $notification->message = $message;
                    $notification->date_created = now();
                    $notification->is_read = false;
                    $notification->save();
                }
                
                // Notify care manager if they exist and are not the one making the change
                if ($careWorker->assigned_care_manager_id && $careWorker->assigned_care_manager_id != Auth::id()) {
                    // Check if care manager exists and is active before creating notification
                    $careManager = User::where('id', $careWorker->assigned_care_manager_id)
                        ->where('role_id', 2)
                        ->where('status', 'Active')
                        ->first();
                        
                    if ($careManager) {
                        $title = ($status == 'Active') ? 'Care Worker Activated' : 'Care Worker Deactivated';
                        $message = 'Care worker ' . $careWorker->first_name . ' ' . $careWorker->last_name . 
                                ' status has been changed to ' . $status . ' by ' . $actor . '.';
                        
                        $notification = new Notification();
                        $notification->user_id = $careManager->id;
                        $notification->user_type = 'cose_staff';
                        $notification->message_title = $title;
                        $notification->message = $message;
                        $notification->date_created = now();
                        $notification->is_read = false;
                        $notification->save();
                    }
                }
            } catch (\Exception $notifyEx) {
                // Log notification error but don't fail the status update
                \Log::warning('Notification error during status update: ' . $notifyEx->getMessage(), [
                    'exception' => $notifyEx,
                    'care_worker_id' => $id
                ]);
            }
            
            \Log::info('Care worker status updated successfully', [
                'care_worker_id' => $id,
                'new_status' => $status
            ]);
            
            return response()->json(['success' => true, 'message' => 'Care worker status updated successfully.']);
        } catch (\Exception $e) {
            \Log::error('Care worker status update failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function deleteCareworker(Request $request)
    {
        try {
            // Use consistent variable naming - using camelCase throughout
            $careWorkerId = $request->input('careworker_id');
            
            // Find the care worker before deletion to get their details
            $careWorker = User::find($careWorkerId);
            
            if (!$careWorker) {
                return response()->json(['success' => false, 'message' => 'Care worker not found.']);
            }
            
            // Store care worker details for notification
            $careWorkerName = $careWorker->first_name . ' ' . $careWorker->last_name;
            $careManagerId = $careWorker->assigned_care_manager_id;
            
            // Check if the care worker has assigned beneficiaries
            $hasAssignedBeneficiaries = GeneralCarePlan::where('care_worker_id', $careWorkerId)->exists();
            
            if ($hasAssignedBeneficiaries) {
                return response()->json([
                    'success' => false,
                    'message' => 'This care worker has assigned beneficiaries and cannot be deleted. Please reassign the beneficiaries first.',
                    'error_type' => 'has_beneficiaries'
                ]);
            }
            
            // Create notification for the assigned care manager (if not the one deleting)
            if ($careManagerId && $careManagerId != Auth::id()) {
                try {
                    $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                    $title = 'Care Worker Deleted';
                    $message = 'Care worker ' . $careWorkerName . ', who was assigned to you, has been deleted by ' . $actor . '.';
                    
                    $notification = new Notification();
                    $notification->user_id = $careManagerId;
                    $notification->user_type = 'cose_staff';
                    $notification->message_title = $title;
                    $notification->message = $message;
                    $notification->date_created = now();
                    $notification->is_read = false;
                    $notification->save();
                } catch (\Exception $notifyEx) {
                    \Log::warning('Failed to send care worker deletion notification: ' . $notifyEx->getMessage());
                    // Continue with deletion anyway - don't let notification failure prevent deletion
                }
            }
            
            // Use the service for deletion
            $result = $this->userManagementService->deleteCareworker(
                $careWorkerId,
                Auth::user()
            );
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            \Log::error('Error during care worker deletion: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'care_worker_id' => $request->input('careworker_id')
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while deleting the care worker: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a notification to a care worker
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
                \Log::warning('Attempted to send notification to non-existent or inactive care worker: ' . $careWorkerId);
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
     * Send a notification to a care manager about a care worker
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
                \Log::warning('Attempted to send notification to non-existent or inactive care manager: ' . $careManagerId);
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

    /**
     * Update the care worker's email
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCareWorkerEmail(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'account_email' => [
                'required',
                'string',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('cose_users', 'email')->ignore(Auth::id()),
            ],
            'current_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('activeTab', 'settings')
                ->withInput();
        }

        // Get the current user
        $user = Auth::user();
        
        // Check if the new email is the same as the current email
        if ($user->email === $request->input('account_email')) {
            return redirect()->back()
                ->withErrors(['account_email' => 'The new email is the same as your current email.'])
                ->with('activeTab', 'settings');
        }

        // Verify current password
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'The provided password does not match your current password.'])
                ->with('activeTab', 'settings');
        }

        try {
            // Update email
            $user->email = $request->input('account_email');
            $user->updated_at = now();
            $user->save();

            // Log the email change
            \Log::info('Care worker email updated', [
                'care_worker_id' => $user->id,
                'old_email' => $user->getOriginal('email'),
                'new_email' => $user->email
            ]);

            return redirect()->route('care-worker.account.profile.index')
                ->with('success', 'Your email has been updated successfully.')
                ->with('activeTab', 'settings');
        } catch (\Exception $e) {
            \Log::error('Failed to update care worker email: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while updating your email. Please try again.'])
                ->with('activeTab', 'settings');
        }
    }

    /**
     * Update the care worker's password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCareWorkerPassword(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'account_password' => 'required|string|min:8',
            'account_password_confirmation' => 'required|same:account_password',
        ], [
            'account_password.required' => 'The new password field is required.',
            'account_password.min' => 'The new password must be at least 8 characters.',
            'account_password_confirmation.required' => 'Please confirm your new password.',
            'account_password_confirmation.same' => 'The password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('activeTab', 'settings')
                ->withInput();
        }

        // Get the current user
        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'The provided password does not match your current password.'])
                ->with('activeTab', 'settings');
        }

        try {
            // Update password
            $user->password = bcrypt($request->input('account_password'));
            $user->updated_at = now();
            $user->save();

            // Log the password change (without revealing the actual password)
            \Log::info('Care worker password updated', [
                'care_worker_id' => $user->id,
                'timestamp' => now()
            ]);

            return redirect()->route('care-worker.account.profile.index')
                ->with('success', 'Your password has been updated successfully.')
                ->with('activeTab', 'settings');
        } catch (\Exception $e) {
            \Log::error('Failed to update care worker password: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while updating your password. Please try again.'])
                ->with('activeTab', 'settings');
        }
    }
}