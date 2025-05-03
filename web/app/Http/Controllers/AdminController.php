<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Models\WeeklyCarePlan;
use App\Models\FamilyMember;
use App\Models\OrganizationRole;
use App\Models\Notification;
use Carbon\Carbon;

use App\Services\UserManagementService;

class AdminController extends Controller
{
    protected $userManagementService;
    
    public function __construct(UserManagementService $userManagementService)
    {
        $this->userManagementService = $userManagementService;
    }

    public function storeAdministrator(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            // Personal Details
            'first_name' => [
                'required',
                'string',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/',
                'max:100'
            ],
            'last_name' => [
                'required',
                'string',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/',
                'max:100'
            ],
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(), // Must be older than 14 years
            'gender' => 'nullable|string|in:Male,Female,Other', // Must match dropdown options
            'civil_status' => 'nullable|string|in:Single,Married,Widowed,Divorced', // Must match dropdown options
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
        
            // Organization Roles
            'Organization_Roles' => [
                'required',
                'integer',
                'exists:organization_roles,organization_role_id',
                function ($attribute, $value, $fail) {
                    // If trying to set role to Executive Director (role_id = 1)
                    // If trying to set role to Executive Director (role_id = 1)
                        if ($value == 1) {
                            // Check if another Executive Director exists
                            $existingExecutiveDirector = User::where('organization_role_id', 1)->exists();
                            
                            if ($existingExecutiveDirector) {
                                $fail('There can only be one Executive Director. Please select a different role.');
                        }
                    }
                },
            ],
        
            // Documents
            'administrator_photo' => 'nullable|image|mimes:jpeg,png|max:2048',
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Handle file uploads and rename files...
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $uniqueIdentifier = time() . '_' . Str::random(5);
    
            if ($request->hasFile('administrator_photo')) {
                $administratorPhotoPath = $request->file('administrator_photo')->storeAs(
                    'uploads/administrator_photos', 
                    $firstName . '' . $lastName . '_photo' . $uniqueIdentifier . '.' . $request->file('administrator_photo')->getClientOriginalExtension(),
                    'public'
                );
            }
    
            if ($request->hasFile('government_ID')) {
                $governmentIDPath = $request->file('government_ID')->storeAs(
                    'uploads/administrator_government_ids', 
                    $firstName . '' . $lastName . '_government_id' . $uniqueIdentifier . '.' . $request->file('government_ID')->getClientOriginalExtension(),
                    'public'
                );
            }
    
            if ($request->hasFile('resume')) {
                $resumePath = $request->file('resume')->storeAs(
                    'uploads/administrator_resumes', 
                    $firstName . '' . $lastName . '_resume' . $uniqueIdentifier . '.' . $request->file('resume')->getClientOriginalExtension(),
                    'public'
                );
            }

        // Save the administrator to the database
        $administrator = new User();
        $administrator->first_name = $request->input('first_name');
        $administrator->last_name = $request->input('last_name');
        // $administrator->name = $request->input('name') . ' ' . $request->input('last_name'); // Combine first and last name
        $administrator->birthday = $request->input('birth_date');
        $administrator->gender = $request->input('gender') ?? null;
        $administrator->civil_status = $request->input('civil_status') ?? null;
        $administrator->religion = $request->input('religion') ?? null;
        $administrator->nationality = $request->input('nationality') ?? null;
        $administrator->educational_background = $request->input('educational_background') ?? null;
        $administrator->address = $request->input('address_details');
        $administrator->email = $request->input('account.email'); // Work email
        $administrator->personal_email = $request->input('personal_email'); // Personal email
        $administrator->mobile = '+63' . $request->input('mobile_number');
        $administrator->landline = $request->input('landline_number') ?? null;
        $administrator->password = bcrypt($request->input('account.password'));
        $administrator->organization_role_id = $request->input('Organization_Roles');
        $administrator->role_id = 1; // 1 is the role ID for administrators
        $administrator->volunteer_status = 'Active'; // Status in COSE
        $administrator->status = 'Active'; // Status for access to the system
        $administrator->status_start_date = now();

        // Save file paths and IDs
        $administrator->photo = $administratorPhotoPath ?? null;
        $administrator->government_issued_id = $request->hasFile('government_ID') ? $governmentIDPath : null;
        $administrator->cv_resume = $request->hasFile('resume') ? $resumePath : null;
        $administrator->sss_id_number = $request->input('sss_ID') ?? null;
        $administrator->philhealth_id_number = $request->input('philhealth_ID') ?? null;
        $administrator->pagibig_id_number = $request->input('pagibig_ID') ?? null;

        // Generate and save the remember_token
        $administrator->remember_token = Str::random(60);


        $administrator->save();

        // Send welcome notification to the new administrator
        $welcomeTitle = 'Welcome to SULONG KALINGA';
        $welcomeMessage = 'Welcome ' . $administrator->first_name . ' ' . $administrator->last_name . 
                         '! Your administrator account has been created. You can now access the system with your credentials.';
        $this->sendNotificationToAdmin($administrator->id, $welcomeTitle, $welcomeMessage);
        
        // Send notification to other admins about the new admin (excluding the creator and the new admin)
        $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $title = 'New Administrator Added';
        $message = $actor . ' added a new administrator ' . $administrator->first_name . ' ' . 
                  $administrator->last_name . ' as ' . 
                  ($administrator->organization_role_id == 2 ? 'Project Coordinator' : 'MEAL Coordinator');
        $this->sendAdminNotification($title, $message, $administrator->id);

        // Redirect with success message
        return redirect()->route('admin.administrators.create')->with('success', 'Administrator has been successfully added!');
        
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
            \Log::error('Database error when creating administrator: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while saving the administrator. Please try again.']);
        } catch (\Exception $e) {
            // For any other unexpected errors
            \Log::error('Unexpected error when creating administrator: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    public function updateAdministrator(Request $request, $id)
    {
        // Find the administrator by ID
        $administrator = User::findOrFail($id);

        // Store original name for notification message
        $originalFirstName = $administrator->first_name;
        $originalLastName = $administrator->last_name;

        // dd([
        //     'method' => $request->method(),
        //     'action' => $request->url(),
        // ]);

        // Validate the input data
        $validator = Validator::make($request->all(), [
            // Personal Details
            'first_name' => [
                'required',
                'string',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/',
                'max:100'
            ],
            'last_name' => [
                'required',
                'string',
                'regex:/^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$/',
                'max:100'
            ],
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(14)->toDateString(),
            'gender' => 'nullable|string|in:Male,Female,Other',

            'civil_status' => 'nullable|string|in:Single,Married,Widowed,Divorced',
            'religion' => 'nullable|string|regex:/^[a-zA-Z\s]*$/',
            'nationality' => 'nullable|string|regex:/^[a-zA-Z\s]*$/',
            'educational_background' => 'nullable|string|in:College,Highschool,Doctorate',

            'address_details' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s,.-]+$/',
            ],
            'personal_email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('cose_users', 'personal_email')->ignore($id),
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
            'administrator_photo' => 'nullable|image|mimes:jpeg,png|max:2048',
            'government_ID' => 'nullable|image|mimes:jpeg,png|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'sss_ID' => 'nullable|string|max:10',
            'philhealth_ID' => 'nullable|string|max:12',
            'pagibig_ID' => 'nullable|string|max:12',
            'account.email' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('cose_users', 'email')->ignore($id),
            ],
            'account.password' => 'nullable|string|min:8|confirmed',
            'administrator_photo' => 'nullable|image|mimes:jpeg,png|max:2048',
            'government_ID' => 'nullable|image|mimes:jpeg,png|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'Organization_Roles' => [
                'required',
                'integer',
                'exists:organization_roles,organization_role_id',
                function ($attribute, $value, $fail) use ($id, $administrator) {
                    // If trying to set role to Executive Director (role_id = 1)
                    if ($value == 1) {
                        // Check if this user is not already the Executive Director
                        if ($administrator->organization_role_id != 1) {
                            // Check if another Executive Director exists
                            $existingExecutiveDirector = User::where('organization_role_id', 1)
                                ->where('id', '!=', $id)
                                ->exists();
                            
                            if ($existingExecutiveDirector) {
                                $fail('There can only be one Executive Director. Please select a different role.');
                            }
                        }
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Generate unique identifier for file naming
        $uniqueIdentifier = time() . '_' . Str::random(5);

    // Handle Administrator Photo
    if ($request->hasFile('administrator_photo')) {
        $directory = public_path('storage/uploads/administrator_photos');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $administratorPhotoPath = $request->file('administrator_photo')->storeAs(
            'uploads/administrator_photos',
            $administrator->first_name . '_' . $administrator->last_name . '_photo_' . $uniqueIdentifier . '.' . $request->file('administrator_photo')->getClientOriginalExtension(),
            'public'
        );
        $administrator->photo = $administratorPhotoPath;
    }

    // Handle Government Issued ID
    if ($request->hasFile('government_ID')) {
        $directory = public_path('storage/uploads/administrator_government_ids');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $governmentIDPath = $request->file('government_ID')->storeAs(
            'uploads/administrator_government_ids',
            $administrator->first_name . '_' . $administrator->last_name . '_government_id_' . $uniqueIdentifier . '.' . $request->file('government_ID')->getClientOriginalExtension(),
            'public'
        );
        $administrator->government_issued_id = $governmentIDPath;
    }

    // Handle Resume
    if ($request->hasFile('resume')) {
        $directory = public_path('storage/uploads/administrator_resumes');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $resumePath = $request->file('resume')->storeAs(
            'uploads/administrator_resumes',
            $administrator->first_name . '_' . $administrator->last_name . '_resume_' . $uniqueIdentifier . '.' . $request->file('resume')->getClientOriginalExtension(),
            'public'
        );
        $administrator->cv_resume = $resumePath;
    }

        // Handle file uploads if new files are provided
        $uniqueIdentifier = time() . '_' . Str::random(5);

        if ($request->hasFile('administrator_photo')) {
            $administratorPhotoPath = $request->file('administrator_photo')->storeAs(
                'uploads/administrator_photos',
                $administrator->first_name . '_' . $administrator->last_name . '_photo_' . $uniqueIdentifier . '.' . $request->file('administrator_photo')->getClientOriginalExtension(),
                'public'
            );
            $administrator->photo = $administratorPhotoPath;
        }

        if ($request->hasFile('government_ID')) {
            $governmentIDPath = $request->file('government_ID')->storeAs(
                'uploads/administrator_government_ids',
                $administrator->first_name . '_' . $administrator->last_name . '_government_id_' . $uniqueIdentifier . '.' . $request->file('government_ID')->getClientOriginalExtension(),
                'public'
            );
            $administrator->government_issued_id = $governmentIDPath;
        }

        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->storeAs(
                'uploads/administrator_resumes',
                $administrator->first_name . '_' . $administrator->last_name . '_resume_' . $uniqueIdentifier . '.' . $request->file('resume')->getClientOriginalExtension(),
                'public'
            );
            $administrator->cv_resume = $resumePath;
        }

        // Update administrator details
        $administrator->first_name = $request->input('first_name');
        $administrator->last_name = $request->input('last_name');
        $administrator->birthday = $request->input('birth_date');
        $administrator->gender = $request->input('gender');
        $administrator->civil_status = $request->input('civil_status');
        $administrator->religion = $request->input('religion');
        $administrator->nationality = $request->input('nationality');
        $administrator->educational_background = $request->input('educational_background');
        $administrator->address = $request->input('address_details');
        $administrator->personal_email = $request->input('personal_email');
        $administrator->mobile = '+63' . $request->input('mobile_number');
        $administrator->landline = $request->input('landline_number');
        $administrator->email = $request->input('account.email');
        $administrator->organization_role_id = $request->input('Organization_Roles');

        // Insert statements for file paths
        if (isset($administratorPhotoPath)) {
            $administrator->photo = $administratorPhotoPath;
        }

        if (isset($governmentIDPath)) {
            $administrator->government_issued_id = $governmentIDPath;
        }

        if (isset($resumePath)) {
            $administrator->cv_resume = $resumePath;
        }

        // Update password if provided
        if ($request->filled('account.password')) {
            $administrator->password = bcrypt($request->input('account.password'));
        }

        // Update IDs
        $administrator->sss_id_number = $request->input('sss_ID');
        $administrator->philhealth_id_number = $request->input('philhealth_ID');
        $administrator->pagibig_id_number = $request->input('pagibig_ID');

        $administrator->updated_by = Auth::id();
        $administrator->updated_at = now();

        // Save the updated administrator
        $administrator->save();

        // Only notify if the user is not updating their own profile
        if (Auth::id() != $administrator->id) {
            // Send notification to the administrator whose details were updated
            $title = 'Your Profile Was Updated';
            $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $message = 'Your administrator profile was updated by ' . $actor . '.';
            $this->sendNotificationToAdmin($administrator->id, $title, $message);
        }

        // Redirect with success message
        return redirect()->route('admin.administrators.index')->with('success', 
        'Administrator ' . $administrator->first_name . ' ' . $administrator->last_name . 
        ' has been successfully updated!'
        );
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');

        // Fetch administrators based on the search query and filters
        $administrators = User::where('role_id', 1)
            ->with('organizationRole')
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                          ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            })
            ->when($filter, function ($query, $filter) {
                if ($filter == 'status') {
                    return $query->orderBy('volunteer_status');
                } elseif ($filter == 'organizationrole') {
                    return $query->orderBy('organization_role_id');
                } elseif ($filter == 'area') {
                    return $query->join('organization_roles', 'users.organization_role_id', '=', 'organization_roles.organization_role_id')
                                 ->orderBy('organization_roles.area');
                }
            })
            ->orderBy('first_name') // Order by first name alphabetically by default
            ->get();

        // Debugging: Check the data
        // dd($administrators);

        // Pass the data to the Blade template
        return view('admin.administratorProfile', compact('administrators'));
    }

    public function viewAdminDetails(Request $request)
    {
        $administrator_id = $request->input('administrator_id');
        $administrator = User::where('role_id', 1)
        ->with('organizationRole')
        ->find($administrator_id);

        if (!$administrator) {
            return redirect()->route('administratorProfile')->with('error', 'Administrator not found.');
        }

        return view('admin.viewAdminDetails', compact('administrator'));
    }

    public function editAdminProfile($id)
    {
        $administrator = User::where('role_id', 1)->where('id', $id)->first();
        
        if (!$administrator) {
            return redirect()->route('admin.administrators.edit')->with('error', 'Administrator not found.');
        }
        
        $birth_date = null;
        if ($administrator->birthday) {
            $birth_date = Carbon::parse($administrator->birthday)->format('Y-m-d');
        }
        
        return view('admin.editAdminProfile', compact('administrator', 'birth_date'));
    }

    public function updateStatusAjax($id, Request $request)
    {
        try {
            \Log::info('Update admin status AJAX request', [
                'admin_id' => $id,
                'status' => $request->input('status')
            ]);
            
            // Find administrator (role_id = 1)
            $admin = User::where('role_id', 1)->find($id);
            
            if (!$admin) {
                return response()->json(['success' => false, 'message' => 'Administrator not found.'], 404);
            }
            
            // Don't allow updating your own status
            if ($admin->id == Auth::id()) {
                return response()->json(['success' => false, 'message' => 'You cannot change your own status.'], 400);
            }
            
            // Get the status directly
            $status = $request->input('status');
            $oldStatus = $admin->status;
            
            // Update ONLY the status column
            $admin->status = $status;
            $admin->updated_at = now();
            $admin->save();
            
            // Only send notifications if admin is active (to avoid sending to inactive users)
            if ($status === 'Active') {
                // Try-catch inside to prevent notification errors from breaking status update
                try {
                    // Send notification to the administrator whose status was changed
                    $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                    
                    // Customize message based on whether it's a reactivation
                    if ($oldStatus === 'Inactive') {
                        // Welcome back message for reactivated admins
                        $title = 'Welcome Back to SULONG KALINGA';
                        $message = 'Welcome back, ' . $admin->first_name . '! Your administrator account has been reactivated by ' . $actor . '. You now have full access to the system again.';
                    } else {
                        // Regular status change message
                        $title = 'Your Account Status Changed';
                        $message = 'Your administrator account status was changed from ' . $oldStatus . ' to ' . $status . ' by ' . $actor . '.';
                    }
                    
                    $notification = new Notification();
                    $notification->user_id = $admin->id;
                    $notification->user_type = 'cose_staff';
                    $notification->message_title = $title;
                    $notification->message = $message;
                    $notification->date_created = now();
                    $notification->is_read = false;
                    $notification->save();
                } catch (\Exception $notifyEx) {
                    \Log::warning('Failed to send admin status notification: ' . $notifyEx->getMessage());
                    // Continue execution - don't let notification failure prevent status update
                }
            }
            
            // If status changed to "Inactive", also notify other admins (it's important)
            if ($status == 'Inactive') {
                try {
                    // Get all active admins except the current user and the target admin
                    $admins = User::where('role_id', 1)
                        ->where('status', 'Active')
                        ->where('id', '!=', Auth::id())
                        ->where('id', '!=', $admin->id)
                        ->get();
                    
                    $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                    $adminTitle = 'Administrator Account Deactivated';
                    $adminMessage = $actor . ' changed the status of administrator ' . $admin->first_name . ' ' . $admin->last_name . ' to Inactive.';
                    
                    foreach ($admins as $otherAdmin) {
                        $notification = new Notification();
                        $notification->user_id = $otherAdmin->id;
                        $notification->user_type = 'cose_staff';
                        $notification->message_title = $adminTitle;
                        $notification->message = $adminMessage;
                        $notification->date_created = now();
                        $notification->is_read = false;
                        $notification->save();
                    }
                } catch (\Exception $notifyEx) {
                    \Log::warning('Failed to send admin deactivation notifications: ' . $notifyEx->getMessage());
                    // Continue execution - don't let notification failure prevent status update
                }
            }
            
            \Log::info('Admin status updated successfully', [
                'admin_id' => $id,
                'new_status' => $status
            ]);
            
            return response()->json(['success' => true, 'message' => 'Administrator status updated successfully.']);
        } catch (\Exception $e) {
            \Log::error('Admin status update failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function deleteAdministrator(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Get the ID from the request
            $id = $request->input('admin_id');
            
            // Find the administrator
            $administrator = User::where('id', $id)
                ->where('role_id', 1)
                ->first();
            
            if (!$administrator) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Administrator not found.'
                ]);
            }

            // Don't allow deleting if they're Executive Director
            if ($administrator->organization_role_id == 1) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Executive Director account cannot be deleted.',
                    'error_type' => 'executive_director'
                ]);
            }

            // Check if current user has permission to delete
            if (Auth::user()->organization_role_id != 1) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Only Executive Director can delete administrator accounts.'
                ]);
            }

            // Check for dependencies before deletion
            $dependencies = [
                'beneficiaries' => Beneficiary::where('created_by', $id)
                    ->orWhere('updated_by', $id)
                    ->exists(),
                'users' => User::where('updated_by', $id)
                    ->exists(),
                'weekly_care_plans' => WeeklyCarePlan::where('created_by', $id)
                    ->orWhere('updated_by', $id)
                    ->exists(),
                'family_members' => FamilyMember::where('created_by', $id)
                    ->orWhere('updated_by', $id)
                    ->exists()
            ];

            foreach ($dependencies as $type => $exists) {
                if ($exists) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "This administrator has created or updated {$type} that require audit history. Deletion is not allowed.",
                        'error_type' => "dependency_{$type}"
                    ]);
                }
            }

            // Delete associated files
            $filesToDelete = [
                'photo' => $administrator->photo,
                'government_issued_id' => $administrator->government_issued_id,
                'cv_resume' => $administrator->cv_resume
            ];

            foreach ($filesToDelete as $file) {
                if ($file) {
                    Storage::disk('public')->delete($file);
                }
            }

            // Delete the administrator
            $administrator->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Administrator deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting administrator: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the administrator. Please try again later.',
                'debug_message' => app()->environment('local') ? $e->getMessage() : null
            ]);
        }
    }

    public function deleteCaremanager(Request $request)
    {
        try {
            // Get the ID from the request
            $id = $request->input('caremanager_id');
            
            Log::info('Starting deletion process for care manager ID: ' . $id);
            
            // Find the care manager
            $caremanager = User::where('id', $id)
                            ->where('role_id', 2) // Care manager role
                            ->first();
            
            if (!$caremanager) {
                Log::warning('Care manager not found: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Care manager not found.'
                ]);
            }
            
            Log::info('Found care manager: ' . $caremanager->first_name . ' ' . $caremanager->last_name);
            
            // Check if the current user is an admin (role_id = 1)
            if (Auth::user()->role_id != 1) {
                Log::warning('Non-admin tried to delete care manager. User ID: ' . Auth::id());
                return response()->json([
                    'success' => false,
                    'message' => 'Only administrators can delete care manager accounts.'
                ]);
            }
            
            // Check for various audit dependencies first before attempting to delete
            Log::info('Checking audit dependencies for care manager ID: ' . $id);
            
            // 1. Check beneficiaries table for created_by/updated_by references
            $beneficiaryReferences = \DB::table('beneficiaries')
                ->where('created_by', $id)
                ->orWhere('updated_by', $id)
                ->count();
            
            Log::info('Beneficiary references count: ' . $beneficiaryReferences);
                
            if ($beneficiaryReferences > 0) {
                Log::info('Care manager has beneficiary references, cannot delete');
                return response()->json([
                    'success' => false,
                    'message' => 'This care manager has created or updated beneficiary records which require audit history to be maintained.',
                    'error_type' => 'dependency_beneficiaries'
                ]);
            }
            
            // 2. Check users table for created_by/updated_by references
            $userReferences = \DB::table('cose_users')
                ->where('updated_by', $id)
                ->count();
            
            Log::info('User references count: ' . $userReferences);
                
            if ($userReferences > 0) {
                Log::info('Care manager has user references, cannot delete');
                return response()->json([
                    'success' => false,
                    'message' => 'This care manager has created or updated user accounts which require audit history to be maintained.',
                    'error_type' => 'dependency_users'
                ]);
            }
            
            // 3. Check weekly care plans table for created_by/updated_by references
            $carePlanReferences = \DB::table('weekly_care_plans')
                ->where('created_by', $id)
                ->orWhere('updated_by', $id)
                ->count();
            
            Log::info('Care plan references count: ' . $carePlanReferences);
                
            if ($carePlanReferences > 0) {
                Log::info('Care manager has care plan references, cannot delete');
                return response()->json([
                    'success' => false,
                    'message' => 'This care manager has created or updated care plans which require audit history to be maintained.',
                    'error_type' => 'dependency_care_plans'
                ]);
            }
            
            // 4. Check family members table for created_by/updated_by references
            $familyReferences = \DB::table('family_members')
                ->where('created_by', $id)
                ->orWhere('updated_by', $id)
                ->count();
            
            Log::info('Family references count: ' . $familyReferences);
                
            if ($familyReferences > 0) {
                Log::info('Care manager has family references, cannot delete');
                return response()->json([
                    'success' => false,
                    'message' => 'This care manager has created or updated family member records which require audit history to be maintained.',
                    'error_type' => 'dependency_family'
                ]);
            }
            
            // If we got here, we can proceed with deletion attempt
            Log::info('No audit dependencies found, proceeding with deletion');
            
            // Begin database transaction
            \DB::beginTransaction();
            
            try {
                
                Log::info('Attempting to delete care manager with ID: ' . $id);
                
                // Try with regular delete first
                try {
                    $result = $caremanager->delete();
                    Log::info('Regular delete result: ' . ($result ? 'success' : 'failed'));
                    
                    if (!$result) {
                        // If regular delete fails, try with force delete
                        Log::info('Regular delete failed, trying force delete');
                        $result = $caremanager->forceDelete();
                        Log::info('Force delete result: ' . ($result ? 'success' : 'failed'));
                        
                        if (!$result) {
                            throw new \Exception('Both regular and force delete failed');
                        }
                    }
                    
                    // If we got here, everything succeeded
                    \DB::commit();
                    Log::info('Care manager deleted successfully: ' . $id);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Care manager deleted successfully.'
                    ]);
                } catch (\Exception $deleteException) {
                    Log::error('Error during delete operation: ' . $deleteException->getMessage());
                    
                    // Try using DB::delete as a last resort
                    Log::info('Trying direct DB::delete');
                    $directDeleteResult = \DB::delete('DELETE FROM cose_users WHERE id = ?', [$id]);
                    Log::info('Direct DB::delete result: ' . $directDeleteResult);
                    
                    if ($directDeleteResult) {
                        \DB::commit();
                        Log::info('Care manager deleted successfully using direct DB::delete: ' . $id);
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Care manager deleted successfully.'
                        ]);
                    } else {
                        throw $deleteException; // Re-throw to be caught by outer catch
                    }
                }
            } catch (\Exception $innerException) {
                // If anything fails, roll back the transaction
                \DB::rollBack();
                Log::error('Inner exception while deleting care manager: ' . $innerException->getMessage());
                Log::error('Stack trace: ' . $innerException->getTraceAsString());
                
                // Check if this is a PDO exception with more details
                if ($innerException instanceof \PDOException) {
                    Log::error('PDO error code: ' . $innerException->getCode());
                    Log::error('SQL state: ' . $innerException->errorInfo[0] ?? 'unknown');
                }
                
                throw $innerException; // Re-throw to be caught by outer catch
            }
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error deleting care manager: ' . $e->getMessage());
            Log::error('Error code: ' . $e->getCode());
            
            // If we can get the SQL that failed, log it
            if (method_exists($e, 'getSql')) {
                Log::error('Failed SQL: ' . $e->getSql());
            }
            
            // Check for foreign key constraint violation
            if (in_array($e->getCode(), ['23000', '23503']) || 
                (is_numeric($e->getCode()) && in_array((int)$e->getCode(), [23000, 23503]))) {
                
                // Try to determine which table has the dependency
                $errorMsg = $e->getMessage();
                Log::error('Full error message: ' . $errorMsg);
                
                // Extract table name using various patterns
                $tableName = '';
                if (preg_match('/table "([^"]+)"/', $errorMsg, $matches)) {
                    $tableName = $matches[1];
                } elseif (preg_match('/table ([^\s]+)/', $errorMsg, $matches)) {
                    $tableName = $matches[1];
                } elseif (preg_match('/on table "([^"]+)"/', $errorMsg, $matches)) {
                    $tableName = $matches[1];
                }
                
                Log::info('Extracted table name: ' . $tableName);
                
                if (stripos($errorMsg, 'beneficiaries') !== false || $tableName == 'beneficiaries') {
                    return response()->json([
                        'success' => false,
                        'message' => 'This care manager has created or updated beneficiary records which require audit history to be maintained.',
                        'error_type' => 'dependency_beneficiaries'
                    ]);
                } else if (stripos($errorMsg, 'cose_users') !== false || stripos($errorMsg, 'users') !== false || $tableName == 'cose_users') {
                    return response()->json([
                        'success' => false,
                        'message' => 'This care manager has created or updated user accounts which require audit history to be maintained.',
                        'error_type' => 'dependency_users'
                    ]);
                } else if (stripos($errorMsg, 'weekly_care_plans') !== false || stripos($errorMsg, 'care_plans') !== false || $tableName == 'weekly_care_plans') {
                    return response()->json([
                        'success' => false,
                        'message' => 'This care manager has created or updated care plans which require audit history to be maintained.',
                        'error_type' => 'dependency_care_plans'
                    ]);
                } else if (stripos($errorMsg, 'family_members') !== false || $tableName == 'family_members') {
                    return response()->json([
                        'success' => false,
                        'message' => 'This care manager has created or updated family member records which require audit history to be maintained.',
                        'error_type' => 'dependency_family'
                    ]);
                }
                
                // If in development, return the actual error details
                if (app()->environment('local', 'development')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Database constraint error: ' . $errorMsg,
                        'error_type' => 'dependency_detail',
                        'code' => $e->getCode(),
                        'table' => $tableName ?: 'unknown'
                    ]);
                }
                
                // Generic dependency message for other tables
                return response()->json([
                    'success' => false,
                    'message' => 'This care manager has created or updated records in the system that require audit history to be maintained.',
                    'error_type' => 'dependency_audit'
                ]);
            }
            
            // If in development, return the actual error
            if (app()->environment('local', 'development')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage(),
                    'error_type' => 'database',
                    'code' => $e->getCode()
                ]);
            }
            
            // Generic message for production
            return response()->json([
                'success' => false,
                'message' => 'A database error occurred. Please try again later or contact the system administrator.',
                'error_type' => 'database'
            ]);
        } catch (\Exception $e) {
            Log::error('General error deleting care manager: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // If in development, return the actual error
            if (app()->environment('local', 'development')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'error_type' => 'general',
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
            
            // Generic message for production
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error_type' => 'general'
            ]);
        }
    }
    
    public function municipality()
    {
        try {
            // Get all municipalities for the dropdown
            $municipalities = Municipality::orderBy('municipality_name')->get();
            
            // Get all barangays with their associated municipality and beneficiary count
            $barangays = Barangay::with('municipality')
                ->withCount('beneficiaries')
                ->orderBy('municipality_id')
                ->orderBy('barangay_name')
                ->get();
            
            return view('admin.municipality', compact('municipalities', 'barangays'));
        } catch (\Exception $e) {
            dd($e->getMessage()); // This will show the error message
        }
    }

    /**
     * Delete a barangay
     */
    public function deleteBarangay(Request $request, $id)
    {
        // Check if user is authorized (admin only)
        if (Auth::user()->role_id != 1) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete barangays.',
                'error_type' => 'permission_denied'
            ]);
        }
        
        // Validate the request
        $validatedData = $request->validate([
            'password' => 'required'
        ]);
        
        // Verify user password
        if (!Hash::check($validatedData['password'], Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password. Deletion cancelled.'
            ]);
        }
        
        try {
            // Find the barangay
            $barangay = Barangay::findOrFail($id);
            $barangayName = $barangay->barangay_name;
            $municipalityName = Municipality::find($barangay->municipality_id)->municipality_name;

            // Check if this barangay has beneficiaries
            $beneficiaryCount = Beneficiary::where('barangay_id', $id)->count();
            if ($beneficiaryCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "This barangay has {$beneficiaryCount} beneficiaries assigned to it. You must reassign them before deleting this barangay.",
                    'error_type' => 'dependency_beneficiaries'
                ]);
            }
            
            // All checks passed, delete the barangay
            $barangay->delete();

            // Send notification about deleted barangay
            $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $title = 'Barangay Deleted';
            $message = $actor . ' deleted barangay ' . $barangayName . ' from ' . $municipalityName . ' municipality';
            $this->sendLocationNotification($title, $message);
                
            return response()->json([
                'success' => true,
                'message' => 'Barangay deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the barangay: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete a municipality
     */
    public function deleteMunicipality(Request $request, $id)
    {
        // Check if user is authorized (admin only)
        if (Auth::user()->role_id != 1) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete municipalities.',
                'error_type' => 'permission_denied'
            ]);
        }
        
        // Validate the request
        $validatedData = $request->validate([
            'password' => 'required'
        ]);
        
        // Verify user password
        if (!Hash::check($validatedData['password'], Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password. Deletion cancelled.'
            ]);
        }
        
        try {
            // Find the municipality
            $municipality = Municipality::findOrFail($id);
            $municipalityName = $municipality->municipality_name;
            
            // Check if this municipality has barangays
            $barangayCount = Barangay::where('municipality_id', $id)->count();
            if ($barangayCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete this municipality because it has {$barangayCount} barangays assigned to it.",
                    'error_type' => 'dependency_barangays'
                ]);
            }
            
            // Check if this municipality has beneficiaries directly assigned to it
            $beneficiaryCount = Beneficiary::where('municipality_id', $id)->count();
            if ($beneficiaryCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete this municipality because it has {$beneficiaryCount} beneficiaries assigned to it.",
                    'error_type' => 'dependency_beneficiaries'
                ]);
            }
            
            // Check if this municipality has care users (workers, managers) assigned to it
            // FIXED: Changed 'municipality_id' to 'assigned_municipality_id'
            $careUsersCount = User::whereIn('role_id', [2, 3]) // Role IDs for care managers and care workers
                                ->where('assigned_municipality_id', $id)
                                ->count();
            
            if ($careUsersCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete this municipality because it has {$careUsersCount} care users assigned to it.",
                    'error_type' => 'dependency_care_users'
                ]);
            }
            
            // All checks passed, delete the municipality
            $municipality->delete();

             // Send notification about deleted municipality
            $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $title = 'Municipality Deleted';
            $message = $actor . ' deleted municipality ' . $municipalityName;
            $this->sendLocationNotification($title, $message);
            
            return response()->json([
                'success' => true,
                'message' => 'Municipality deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the municipality: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Add a new municipality
     */
    public function addMunicipality(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'municipality_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][A-Za-z][A-Za-z0-9\s\.\-\']*$/', // Must start with capital letter + min 2 letters
                'unique:municipalities,municipality_name'
            ]
        ], [
            'municipality_name.unique' => 'This municipality already exists in the database.',
            'municipality_name.regex' => 'Municipality name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes.',
            'municipality_name.required' => 'The municipality name is required.',
            'municipality_name.max' => 'Municipality name cannot exceed 100 characters.'
        ]);
        
        // Check if validation fails
        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }
            
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            // Create the new municipality
            $municipality = new Municipality();
            $municipality->municipality_name = $request->municipality_name;
            $municipality->province_id = 1; // Default to Northern Samar
            $municipality->save();

            // Send notification about new municipality
            $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $title = 'New Municipality Added';
            $message = $actor . ' added a new municipality ' . $request->municipality_name;
            $this->sendLocationNotification($title, $message);
                
            $message = 'Municipality "' . $request->municipality_name . '" has been added successfully.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            
            return redirect()->route('municipality')->with('success', $message);
        } catch (\Exception $e) {
            $message = 'An error occurred while adding the municipality: ' . $e->getMessage();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            
            return redirect()->route('municipality')->with('error', $message);
        }
    }

    /**
     * Add a new barangay
     */
    public function addBarangay(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'barangay_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][A-Za-z][A-Za-z0-9\s\.\-\']*$/' // Must start with capital letter + min 2 letters
            ],
            'municipality_id' => 'required|exists:municipalities,municipality_id'
        ], [
            'barangay_name.regex' => 'Barangay name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes.',
            'barangay_name.required' => 'The barangay name is required.',
            'barangay_name.max' => 'Barangay name cannot exceed 100 characters.',
            'municipality_id.required' => 'You must select a municipality.',
            'municipality_id.exists' => 'The selected municipality does not exist.'
        ]);
        
        // Check if validation fails
        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }
            
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            // Check if barangay with the same name already exists in this municipality
            $existingBarangay = Barangay::where('barangay_name', $request->barangay_name)
                ->where('municipality_id', $request->municipality_id)
                ->first();
                
            if ($existingBarangay) {
                $municipalityName = Municipality::find($request->municipality_id)->municipality_name;
                $message = 'Barangay "' . $request->barangay_name . '" already exists in ' . $municipalityName . ' municipality.';
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ]);
                }
                
                return redirect()->route('municipality')->with('error', $message);
            }
            
            // Create the new barangay
            $barangay = new Barangay();
            $barangay->barangay_name = $request->barangay_name;
            $barangay->municipality_id = $request->municipality_id;
            $barangay->save();
            
            $municipalityName = Municipality::find($request->municipality_id)->municipality_name;
            $message = 'Barangay "' . $request->barangay_name . '" has been added successfully to ' . $municipalityName . ' municipality.';
            
            // Send notification about new barangay
            $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $title = 'New Barangay Added';
            $message = $actor . ' added a new barangay ' . $request->barangay_name . ' in ' . $municipalityName . ' municipality';
            $this->sendLocationNotification($title, $message);
           
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            
            return redirect()->route('municipality')->with('success', $message);
        } catch (\Exception $e) {
            $message = 'An error occurred while adding the barangay: ' . $e->getMessage();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            
            return redirect()->route('municipality')->with('error', $message);
        }
    }

    /**
     * Update an existing municipality
     */
    public function updateMunicipality(Request $request)
    {
        // Validate the request
        $municipality = Municipality::findOrFail($request->municipality_id);
        $oldName = $municipality->municipality_name;
        
        $validator = Validator::make($request->all(), [
            'municipality_id' => 'required|exists:municipalities,municipality_id',
            'municipality_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][A-Za-z][A-Za-z0-9\s\.\-\']*$/',
                Rule::unique('municipalities', 'municipality_name')->ignore($municipality->municipality_id, 'municipality_id')
            ]
        ]);
        
        // Check if validation fails
        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }
            
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            // Update the municipality
            $municipality->municipality_name = $request->municipality_name;
            $municipality->save();

            // Send notification about updated municipality
            $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $title = 'Municipality Updated';
            $message = $actor . ' updated municipality from "' . $oldName . '" to "' . $request->municipality_name . '"';
            $this->sendLocationNotification($title, $message);
            
            $message = 'Municipality has been updated successfully to "' . $request->municipality_name . '".';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            
            return redirect()->route('municipality')->with('success', $message);
        } catch (\Exception $e) {
            $message = 'An error occurred while updating the municipality: ' . $e->getMessage();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            
            return redirect()->route('municipality')->with('error', $message);
        }
    }

    /**
     * Update an existing barangay
     */
    public function updateBarangay(Request $request)
    {
        // Find the barangay
        $barangay = Barangay::findOrFail($request->barangay_id);
        
        // Store original values for comparison
        $originalName = $barangay->barangay_name;
        $originalMunicipalityId = $barangay->municipality_id;
        $originalMunicipality = Municipality::find($originalMunicipalityId)->municipality_name;

        // Check if anything changed
        if ($request->barangay_name === $originalName && $request->municipality_id == $originalMunicipalityId) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'general' => ['No changes were made. Please modify the barangay name or municipality to update.']
                    ]
                ]);
            }
            
            return redirect()->back()->with('error', 'No changes were made. Please modify the barangay name or municipality to update.');
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'barangay_id' => 'required|exists:barangays,barangay_id',
            'municipality_id' => 'required|exists:municipalities,municipality_id',
            'barangay_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z][A-Za-z][A-Za-z0-9\s\.\-\']*$/',
                Rule::unique('barangays', 'barangay_name')
                    ->where('municipality_id', $request->municipality_id)
                    ->ignore($barangay->barangay_id, 'barangay_id')
            ]
        ], [
            'barangay_name.unique' => 'This barangay name already exists in the selected municipality.',
            'barangay_name.regex' => 'Barangay name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes.',
            'barangay_name.required' => 'The barangay name is required.',
            'barangay_name.max' => 'Barangay name cannot exceed 100 characters.',
            'municipality_id.required' => 'Please select a municipality.'
        ]);
        
        // Check if validation fails
        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }
            
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            // Update the barangay
            $barangay->barangay_name = $request->barangay_name;
            $barangay->municipality_id = $request->municipality_id;
            $barangay->save();

            // Send notification about updated barangay
            $actor = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $title = 'Barangay Updated';
            $message = $actor . ' updated barangay';
            
            if ($originalName != $request->barangay_name) {
                $message .= ' from "' . $originalName . '" to "' . $request->barangay_name . '"';
            } else {
                $message .= ' "' . $originalName . '"';
            }
            
            if ($originalMunicipalityId != $request->municipality_id) {
                $message .= ' and moved it from ' . $originalMunicipality . ' to ' . $newMunicipality->municipality_name;
            }
            
            $this->sendLocationNotification($title, $message);
        
            
            // Prepare success message
            if ($request->municipality_id != $originalMunicipalityId) {
                $newMunicipality = Municipality::find($request->municipality_id);
                $message = "Barangay has been updated and moved to {$newMunicipality->municipality_name} municipality.";
            } else {
                $message = "Barangay has been updated to \"{$request->barangay_name}\".";
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            
            return redirect()->route('municipality')->with('success', $message);
        } catch (\Exception $e) {
            $message = 'An error occurred while updating the barangay: ' . $e->getMessage();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            
            return redirect()->route('municipality')->with('error', $message);
        }
    }

    /**
     * Send location change notification to all admins and care managers (except the current user)
     *
     * @param string $title Notification title
     * @param string $message Notification message
     * @return void
     */
    private function sendLocationNotification($title, $message)
    {
        try {
            // Get the current user's ID
            $currentUserId = Auth::id();
            
            // Get all admins and care managers (roles 1 and 2) EXCEPT the current user
            $staffUsers = User::whereIn('role_id', [1, 2])
                ->where('status', 'Active')
                ->where('id', '!=', $currentUserId) // Exclude the current user
                ->get();
                
            \Log::info('Sending location notification to ' . $staffUsers->count() . ' staff members (excluding author)');
                
            foreach ($staffUsers as $user) {
                // Create notification for each user
                $notification = new \App\Models\Notification();
                $notification->user_id = $user->id;
                $notification->user_type = 'cose_staff'; // All staff users
                $notification->message_title = $title;
                $notification->message = $message;
                $notification->date_created = now();
                $notification->is_read = false;
                $notification->save();
                
                \Log::info('Created notification for user ' . $user->id);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send location notification: ' . $e->getMessage());
        }
    }

    /**
     * Send admin-related notification to all admins (except the current user and optionally the target user)
     *
     * @param string $title Notification title
     * @param string $message Notification message
     * @param int|null $targetAdminId ID of the admin being acted upon (to exclude from recipients)
     * @return void
     */
    private function sendAdminNotification($title, $message, $targetAdminId = null)
    {
        try {
            // Get the current user's ID
            $currentUserId = Auth::id();
            
            // Get all admins (role_id = 1) EXCEPT the current user and the target user
            $adminQuery = User::where('role_id', 1)
                ->where('status', 'Active')
                ->where('id', '!=', $currentUserId);
                
            // Also exclude the target admin if specified
            if ($targetAdminId !== null) {
                $adminQuery->where('id', '!=', $targetAdminId);
            }
            
            $admins = $adminQuery->get();
                
            \Log::info('Sending admin notification to ' . $admins->count() . ' admins (excluding author and target)');
                
            foreach ($admins as $admin) {
                // Create notification for each admin
                $notification = new \App\Models\Notification();
                $notification->user_id = $admin->id;
                $notification->user_type = 'cose_staff';
                $notification->message_title = $title;
                $notification->message = $message;
                $notification->date_created = now();
                $notification->is_read = false;
                $notification->save();
                
                \Log::info('Created admin notification for user ' . $admin->id);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send admin notification: ' . $e->getMessage());
        }
    }

    /**
     * Send a notification specifically to a target admin
     *
     * @param int $adminId ID of the admin to notify
     * @param string $title Notification title  
     * @param string $message Notification message
     * @return void
     */
    private function sendNotificationToAdmin($adminId, $title, $message)
    {
        try {
            // Ensure admin exists and is active
            $admin = User::where('id', $adminId)
                ->where('role_id', 1)
                ->where('status', 'Active')
                ->first();
                
            if (!$admin) {
                \Log::warning('Attempted to send notification to non-existent or inactive admin: ' . $adminId);
                return;
            }
            
            // Create notification
            $notification = new \App\Models\Notification();
            $notification->user_id = $adminId;
            $notification->user_type = 'cose_staff';
            $notification->message_title = $title;
            $notification->message = $message;
            $notification->date_created = now();
            $notification->is_read = false;
            $notification->save();
            
            \Log::info('Created personal notification for admin ' . $adminId);
        } catch (\Exception $e) {
            \Log::error('Failed to send notification to admin ' . $adminId . ': ' . $e->getMessage());
        }
    }

    /**
     * Update the administrator's email
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAdminEmail(Request $request)
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
            \Log::info('Administrator email updated', [
                'admin_id' => $user->id,
                'old_email' => $user->getOriginal('email'),
                'new_email' => $user->email
            ]);

            return redirect()->route('admin.account.profile.index')
                ->with('success', 'Your email has been updated successfully.')
                ->with('activeTab', 'settings');
        } catch (\Exception $e) {
            \Log::error('Failed to update administrator email: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while updating your email. Please try again.'])
                ->with('activeTab', 'settings');
        }
    }

    /**
     * Update the administrator's password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAdminPassword(Request $request)
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
            \Log::info('Administrator password updated', [
                'admin_id' => $user->id,
                'timestamp' => now()
            ]);

            return redirect()->route('admin.account.profile.index')
                ->with('success', 'Your password has been updated successfully.')
                ->with('activeTab', 'settings');
        } catch (\Exception $e) {
            \Log::error('Failed to update administrator password: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while updating your password. Please try again.'])
                ->with('activeTab', 'settings');
        }
    }

}