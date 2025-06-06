<?php

namespace App\Http\Controllers;

use App\Models\EmergencyNotice;
use App\Models\EmergencyUpdate;
use App\Models\EmergencyType;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestUpdate;
use App\Models\ServiceRequestType;
use App\Models\Notification;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Services\LogService;
use App\Enums\LogType;
use Exception;

class EmergencyAndRequestController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Display the main emergency and service request page
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role_id;
        
        // Get emergency notice data (only new and in_progress)
        $emergencyNotices = EmergencyNotice::with(['beneficiary', 'emergencyType', 'sender', 'actionTakenBy', 'updates'])
            ->whereIn('status', ['new', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get service request data (only new and approved)
        $serviceRequests = ServiceRequest::with(['beneficiary', 'serviceType', 'sender', 'actionTakenBy', 'updates', 'careWorker'])
            ->whereIn('status', ['new', 'approved'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Count statistics for display
        $newEmergencyCount = $emergencyNotices->where('status', 'new')->count();
        $inProgressEmergencyCount = $emergencyNotices->where('status', 'in_progress')->count();
        
        $newServiceRequestCount = $serviceRequests->where('status', 'new')->count();
        $approvedServiceRequestCount = $serviceRequests->where('status', 'approved')->count();
        
        // Log this access
        $this->logService->createLog(
            'emergency_request',
            0,
            LogType::VIEW,
            $user->first_name . ' ' . $user->last_name . ' viewed emergency and service requests.',
            $user->id
        );
        
        // Return view based on user role
        if ($role === 1) {
            // Admin view
            return view('admin.adminEmergencyRequest', compact(
                'emergencyNotices',
                'serviceRequests',
                'newEmergencyCount',
                'inProgressEmergencyCount',
                'newServiceRequestCount',
                'approvedServiceRequestCount'
            ));
        } elseif ($role === 2) {
            // Care Manager view
            return view('careManager.careManagerEmergencyRequest', compact(
                'emergencyNotices',
                'serviceRequests',
                'newEmergencyCount',
                'inProgressEmergencyCount',
                'newServiceRequestCount',
                'approvedServiceRequestCount'
            ));
        } else {
            // Care Worker view (read-only)
            return view('careWorker.careWorkerEmergencyRequest', compact(
                'emergencyNotices',
                'serviceRequests',
                'newEmergencyCount',
                'inProgressEmergencyCount',
                'newServiceRequestCount',
                'approvedServiceRequestCount'
            ));
        }
    }

    /**
     * Display history of emergency notices and service requests
     */
    public function viewHistory()
    {
        $user = Auth::user();
        $role = $user->role_id;
        
        // Default to last 30 days
        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        // Get resolved emergency notices with filter
        $resolvedEmergencies = EmergencyNotice::with(['beneficiary.barangay', 'beneficiary.municipality', 'emergencyType', 'sender', 'actionTakenBy', 'updates'])
            ->where('status', 'resolved')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Get both completed AND rejected service requests
        $completedServiceRequests = ServiceRequest::with(['beneficiary.barangay', 'beneficiary.municipality', 'serviceType', 'sender', 'actionTakenBy', 'careWorker', 'updates'])
            ->whereIn('status', ['completed', 'rejected'])
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Debug logging
        \Log::info('History: Total completed/rejected service requests: ' . $completedServiceRequests->count());
        \Log::info('History: Completed requests: ' . $completedServiceRequests->where('status', 'completed')->count());
        \Log::info('History: Rejected requests: ' . $completedServiceRequests->where('status', 'rejected')->count());
        
        // Get current emergencies/requests for statistics
        $emergencyNotices = EmergencyNotice::whereIn('status', ['new', 'in_progress'])->get();
        $serviceRequests = ServiceRequest::whereIn('status', ['new', 'approved'])->get();
        
        // Get statistics
        $emergencyTypeStats = EmergencyNotice::whereIn('status', ['archived', 'resolved'])
        ->whereBetween('updated_at', [$startDate, $endDate])
        ->selectRaw('emergency_type_id, COUNT(*) as count')
        ->groupBy('emergency_type_id')
        ->get()
        ->map(function ($item) {
            $type = EmergencyType::find($item->emergency_type_id);
            return [
                'name' => $type ? $type->name : 'Unknown',
                'count' => $item->count,
                'color' => $type ? $type->color_code : '#6c757d'
            ];
        });
        
        $serviceTypeStats = ServiceRequest::whereIn('status', ['archived', 'completed', 'rejected'])
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->selectRaw('service_type_id, COUNT(*) as count')
            ->groupBy('service_type_id')
            ->get()
            ->map(function ($item) {
                $type = ServiceRequestType::find($item->service_type_id);
                return [
                    'name' => $type ? $type->name : 'Unknown',
                    'count' => $item->count,
                    'color' => $type ? $type->color_code : '#6c757d'
                ];
            });

        $dateRangeLabel = 'Last 30 days';
        
        // Log this access
        $this->logService->createLog(
            'emergency_request_history',
            0,
            LogType::VIEW,
            $user->first_name . ' ' . $user->last_name . ' viewed emergency and service request history.',
            $user->id
        );
        
        // Return view based on user role
       if ($role === 1) {
            return view('admin.adminEmergencyRequestHistory', compact(
                'resolvedEmergencies',
                'completedServiceRequests',
                'emergencyNotices',        // Added these
                'serviceRequests',         // Added these
                'emergencyTypeStats',
                'serviceTypeStats',
                'dateRangeLabel',
                'startDate',
                'endDate'
            ));
        } elseif ($role === 2) {
            return view('careManager.careManagerEmergencyRequestHistory', compact(
                'resolvedEmergencies',
                'completedServiceRequests',
                'emergencyNotices',        // Added these
                'serviceRequests',         // Added these
                'emergencyTypeStats',
                'serviceTypeStats',
                'dateRangeLabel',
                'startDate',
                'endDate'
            ));
        } else {
            return view('careWorker.careWorkerEmergencyRequestHistory', compact(
                'resolvedEmergencies',
                'completedServiceRequests',
                'emergencyNotices',        // Added these
                'serviceRequests',         // Added these
                'emergencyTypeStats',
                'serviceTypeStats',
                'dateRangeLabel',
                'startDate',
                'endDate'
            ));
        }
    }

    /**
     * Filter history by date range
     */
    public function filterHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_range' => 'required|string',
            'start_date' => 'required_if:time_range,custom|date',
            'end_date' => 'required_if:time_range,custom|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Calculate date range
        $endDate = Carbon::now()->endOfDay();
        $timeRange = $request->input('time_range');
        $dateRangeLabel = '';
        
        if ($timeRange === 'custom') {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $dateRangeLabel = $startDate->format('M j, Y') . ' to ' . $endDate->format('M j, Y');
        } else {
            $days = (int)$timeRange;
            $startDate = Carbon::now()->subDays($days)->startOfDay();
            $dateRangeLabel = "Last {$days} days";
        }
        
        // Get filtered data - UPDATED: Changed to 'resolved' to match viewHistory()
        $resolvedEmergencies = EmergencyNotice::with(['beneficiary', 'emergencyType', 'sender', 'actionTakenBy', 'updates'])
            ->where('status', 'resolved') // Changed from 'archived' to 'resolved'
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Similar changes to this method...
        $completedServiceRequests = ServiceRequest::with(['beneficiary.barangay', 'beneficiary.municipality', 'serviceType', 'sender', 'actionTakenBy', 'careWorker', 'updates'])
            ->whereIn('status', ['completed', 'rejected'])
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get();
            
        // Debug logging
        \Log::info('Filter: Total completed/rejected service requests: ' . $completedServiceRequests->count());
        \Log::info('Filter: Completed requests: ' . $completedServiceRequests->where('status', 'completed')->count());
        \Log::info('Filter: Rejected requests: ' . $completedServiceRequests->where('status', 'rejected')->count());
        
        // Get stats for current period
        $pendingEmergencyCount = EmergencyNotice::whereIn('status', ['new', 'in_progress'])->count();
        $pendingServiceCount = ServiceRequest::whereIn('status', ['new', 'approved'])->count();
        
        // Get emergency statistics - UPDATED to match viewHistory()
        $emergencyTypeStats = EmergencyNotice::whereIn('status', ['resolved'])
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->selectRaw('emergency_type_id, COUNT(*) as count')
            ->groupBy('emergency_type_id')
            ->get()
            ->map(function ($item) {
                $type = EmergencyType::find($item->emergency_type_id);
                return [
                    'name' => $type ? $type->name : 'Unknown',
                    'count' => $item->count,
                    'color_code' => $type ? $type->color_code : '#6c757d'  // Make sure this matches what the view expects
                ];
            });
        
        $serviceTypeStats = ServiceRequest::whereIn('status', ['completed', 'rejected'])
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->selectRaw('service_type_id, COUNT(*) as count')
            ->groupBy('service_type_id')
            ->get()
            ->map(function ($item) {
                $type = ServiceRequestType::find($item->service_type_id);
                return [
                    'name' => $type ? $type->name : 'Unknown',
                    'count' => $item->count,
                    'color' => $type ? $type->color_code : '#6c757d'
                ];
            });

        // Calculate statistics
        $emergencyStats = [
            'total' => $resolvedEmergencies->count() + $pendingEmergencyCount,
            'resolved' => $resolvedEmergencies->count(),
            'pending' => $pendingEmergencyCount,
            'byType' => $emergencyTypeStats  // This needs to be included in the response
        ];

        $serviceStats = [
            'total' => $completedServiceRequests->count() + $pendingServiceCount,
            'completed' => $completedServiceRequests->where('status', 'completed')->count(),
            'rejected' => $completedServiceRequests->where('status', 'rejected')->count(),
            'pending' => $pendingServiceCount,
            'byType' => $serviceTypeStats
        ];

        return response()->json([
            'success' => true,
            'resolvedEmergencies' => $resolvedEmergencies, // Changed from archivedEmergencies to match viewHistory()
            'completedServiceRequests' => $completedServiceRequests, // Changed from archivedServiceRequests to match viewHistory()
            'emergencyStats' => $emergencyStats,
            'serviceStats' => $serviceStats,
            'emergencyTypeStats' => $emergencyTypeStats,
            'serviceTypeStats' => $serviceTypeStats,
            'dateRangeLabel' => $dateRangeLabel
        ]);
    }

    /**
     * Respond to an emergency notice
     */
    public function respondToEmergency(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notice_id' => 'required|integer|exists:emergency_notices,notice_id',
            'update_type' => 'required|string|in:response,status_change,assignment,resolution,note',
            'message' => 'required|string|max:1000',
            'status_change_to' => 'nullable|string|in:new,in_progress,resolved,archived',
            'password' => 'required_if:update_type,resolution|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        // Verify password if provided (for sensitive actions like resolution)
        if ($request->update_type == 'resolution' && (!$request->has('password') || !\Hash::check($request->password, $user->password))) {
            return response()->json([
                'success' => false,
                'errors' => ['password' => ['Incorrect password. Please try again.']]
            ], 422);
        }

        try {
            // Find the emergency notice
            $notice = EmergencyNotice::findOrFail($request->notice_id);
            
            // If this is the first response to a new emergency, automatically set to in_progress
            $isFirstResponse = $notice->status === 'new';
            
            // Create update record
            $update = new EmergencyUpdate([
                'notice_id' => $request->notice_id,
                'message' => $request->message,
                'update_type' => $request->update_type,
                'updated_by' => $user->id
            ]);
            
            // Handle status change if requested
            if ($request->update_type === 'status_change' && $request->status_change_to) {
                $update->status_change_to = $request->status_change_to;
                $notice->status = $request->status_change_to;
                $notice->read_status = true;
                $notice->read_at = now();
                $notice->action_type = $request->status_change_to;
                $notice->action_taken_by = $user->id;
                $notice->action_taken_at = now();
            }
            // Handle resolution
            else if ($request->update_type === 'resolution') {
                $update->status_change_to = 'resolved';
                $notice->status = 'resolved';
                $notice->action_type = 'resolved';
                $notice->action_taken_by = $user->id;
                $notice->action_taken_at = now();
            }
            // Handle assignment
            else if ($request->update_type === 'assignment') {
                $notice->assigned_to = $request->assigned_to ?? $user->id;
            }
            // Automatic status change to in_progress for first response of any kind
            else if ($isFirstResponse) {
                $update->status_change_to = 'in_progress';
                $notice->status = 'in_progress';
                $notice->action_type = 'in_progress';
                $notice->action_taken_by = $user->id;
                $notice->action_taken_at = now();
            }
            
            // If this was a new notice, mark it as read
            if ($notice->status === 'new') {
                $notice->read_status = true;
                $notice->read_at = now();
            }
            
            // Always update the assigned_to field if it's the first response and not set yet
            if ($isFirstResponse && !$notice->assigned_to) {
                $notice->assigned_to = $user->id;
            }
            
            // Save changes
            $update->save();
            $notice->save();
            
            // Send notifications to relevant parties
            $this->sendEmergencyNotifications($notice, $update, $user);
            
            // Log this action
            $this->logService->createLog(
                'emergency_notice',
                $notice->notice_id,
                LogType::UPDATE,
                $user->first_name . ' ' . $user->last_name . ' responded to emergency notice #' . $notice->notice_id . '.',
                $user->id
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Response added successfully',
                'notice' => $notice->fresh(['beneficiary', 'emergencyType', 'sender', 'updates', 'assignedUser', 'actionTakenBy'])
            ]);
        } catch (Exception $e) {
            \Log::error('Error responding to emergency: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add response: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle service request actions (approve, reject, complete)
     */
    public function handleServiceRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_request_id' => 'required|exists:service_requests,service_request_id',
            'update_type' => 'required|in:approval,rejection,assignment,completion,note',
            'message' => 'required|string|max:500',
            'care_worker_id' => 'nullable|exists:users,id',
            'password' => 'required_if:update_type,completion|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $user = Auth::user();
        
        try {
            // Get the service request
            $serviceRequest = ServiceRequest::findOrFail($request->service_request_id);
            
            // Validate state transitions
            if ($request->update_type == 'completion' && $serviceRequest->status != 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved service requests can be marked as completed.'
                ], 422);
            }

            if ($request->update_type == 'approval' && $serviceRequest->status == 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'This service request is already approved.'
                ], 422);
            }

            // Additional validation: require care_worker_id for approval
            if ($request->update_type === 'approval' && !$request->filled('care_worker_id')) {
                return response()->json([
                    'success' => false,
                    'errors' => ['care_worker_id' => ['Please assign a care worker when approving a service request.']],
                    'message' => 'Care worker assignment is required'
                ], 422);
            }

            if ($request->update_type == 'completion' && (!$request->has('password') || !Hash::check($request->password, $user->password))) {
                return response()->json([
                    'success' => false,
                    'errors' => ['password' => ['The provided password is incorrect. Please try again.']],
                    'message' => 'Password verification failed'
                ], 422);
            }
            
            // Verify password if provided (for completion)
            if ($request->update_type == 'completion' && (!$request->has('password') || !Hash::check($request->password, $user->password))) {
                return response()->json([
                    'success' => false,
                    'errors' => ['password' => ['Incorrect password']],
                    'message' => 'Password verification failed'
                ], 422);
            }

            // Create update record
            $update = new ServiceRequestUpdate();
            $update->service_request_id = $serviceRequest->service_request_id;
            $update->message = $request->message;
            $update->update_type = $request->update_type;
            $update->updated_by = $user->id;
            
            // Handle different update types - set status automatically based on action
            switch ($request->update_type) {
                case 'approval':
                    $update->status_change_to = 'approved';
                    $serviceRequest->status = 'approved';
                    $serviceRequest->action_type = 'approved';
                    $serviceRequest->action_taken_by = $user->id;
                    $serviceRequest->action_taken_at = now();
                    
                    // Assign care worker if provided
                    if ($request->filled('care_worker_id')) {
                        $serviceRequest->care_worker_id = $request->care_worker_id;
                    }
                    break;
                    
                case 'rejection':
                    $update->status_change_to = 'rejected';
                    $serviceRequest->status = 'rejected';
                    $serviceRequest->action_type = 'rejected';
                    $serviceRequest->action_taken_by = $user->id;
                    $serviceRequest->action_taken_at = now();
                    break;
                    
                case 'assignment':
                    if ($request->filled('care_worker_id')) {
                        $serviceRequest->care_worker_id = $request->care_worker_id;
                    }
                    break;
                    
                case 'completion':
                    $update->status_change_to = 'completed';
                    $serviceRequest->status = 'completed';
                    $serviceRequest->action_type = 'completed';
                    $serviceRequest->action_taken_by = $user->id;
                    $serviceRequest->action_taken_at = now();
                    break;
            }
            
            // Always mark as read
            $serviceRequest->read_status = true;
            $serviceRequest->read_at = $serviceRequest->read_at ?? now();
            
            // Save changes
            $serviceRequest->save();
            $update->save();
            
            // Send notifications
            $this->sendServiceRequestNotifications($serviceRequest, $update, $user);
            
            // Log this action
            $this->logService->createLog(
                'service_request',
                $serviceRequest->service_request_id,
                LogType::UPDATE,
                $user->first_name . ' ' . $user->last_name . ' updated service request #' . $serviceRequest->service_request_id . ' with ' . $update->update_type,
                $user->id
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Service request updated successfully',
                'is_completed_or_rejected' => in_array($serviceRequest->status, ['completed', 'rejected'])
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service request: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Archive a record (emergency notice or service request)
     */
    public function archiveRecord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'record_id' => 'required|integer',
            'record_type' => 'required|in:emergency,service',
            'password' => 'required',
            'note' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        // Verify password
        if (!\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password verification failed',
                'errors' => ['password' => ['The provided password is incorrect.']]
            ], 422);
        }

        try {
            \DB::beginTransaction();
            
            if ($request->record_type === 'emergency') {
                $record = EmergencyNotice::findOrFail($request->record_id);
                $record->status = 'archived';
                $record->action_type = 'resolved';
                $record->action_taken_by = $user->id;
                $record->action_taken_at = now();
                $record->save();
                
                // Create an update record
                if ($request->filled('note')) {
                    EmergencyUpdate::create([
                        'notice_id' => $record->notice_id,
                        'message' => $request->note ?? 'Record archived',
                        'update_type' => 'status_change',
                        'status_change_to' => 'archived',
                        'updated_by' => $user->id
                    ]);
                }
                
                $this->logService->createLog(
                    'emergency_notice',
                    $record->notice_id,
                    LogType::UPDATE,
                    $user->first_name . ' ' . $user->last_name . ' archived emergency notice #' . $record->notice_id,
                    $user->id
                );
            } else {
                $record = ServiceRequest::findOrFail($request->record_id);
                $record->status = 'archived';
                $record->action_taken_by = $user->id;
                $record->action_taken_at = now();
                $record->save();
                
                // Create an update record
                if ($request->filled('note')) {
                    ServiceRequestUpdate::create([
                        'service_request_id' => $record->service_request_id,
                        'message' => $request->note ?? 'Record archived',
                        'update_type' => 'note',
                        'status_change_to' => 'archived',
                        'updated_by' => $user->id
                    ]);
                }
                
                $this->logService->createLog(
                    'service_request',
                    $record->service_request_id,
                    LogType::UPDATE,
                    $user->first_name . ' ' . $user->last_name . ' archived service request #' . $record->service_request_id,
                    $user->id
                );
            }
            
            \DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Record archived successfully'
            ]);
            
        } catch (Exception $e) {
            \DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while archiving the record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Allow care workers to send reminder notifications about unaddressed items
     */
    public function sendReminder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'record_id' => 'required|integer',
            'record_type' => 'required|in:emergency,service',
            'message' => 'required|string|max:500'  // Changed from 'nullable' to 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Please provide a reminder message' // More specific message
            ], 422);
        }

        $user = Auth::user();
        
        // Care workers can only send reminders
        if ($user->role_id !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Only care workers can send reminders'
            ], 403);
        }

        try {
            $careManager = User::find($user->assigned_care_manager_id);
            
            if (!$careManager) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have an assigned care manager to notify'
                ], 400);
            }
            
            if ($request->record_type === 'emergency') {
                $record = EmergencyNotice::with('beneficiary')->findOrFail($request->record_id);
                $typeName = 'emergency notice';
            } else {
                $record = ServiceRequest::with('beneficiary')->findOrFail($request->record_id);
                $typeName = 'service request';
            }
            
            // Create the notification
            $message = $request->message ?? "This {$typeName} requires your attention.";
            $title = "Reminder: Pending {$typeName} for " . $record->beneficiary->first_name . ' ' . $record->beneficiary->last_name;
            
            Notification::create([
                'user_id' => $careManager->id,
                'user_type' => 'cose_staff',
                'message_title' => $title,
                'message' => $message . " (Reminder from " . $user->first_name . " " . $user->last_name . ")",
                'date_created' => now(),
                'is_read' => false
            ]);
            
            // Log the reminder
            $this->logService->createLog(
                $request->record_type === 'emergency' ? 'emergency_notice' : 'service_request',
                $request->record_id,
                LogType::UPDATE,
                $user->first_name . ' ' . $user->last_name . ' sent a reminder about ' . $typeName . ' #' . $request->record_id,
                $user->id
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Reminder sent successfully to your care manager'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the reminder',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notifications to relevant stakeholders about emergency notice updates
     */
    private function sendEmergencyNotifications(EmergencyNotice $notice, EmergencyUpdate $update, User $actor)
    {
        // Get beneficiary
        $beneficiary = Beneficiary::find($notice->beneficiary_id);
        if (!$beneficiary) return;
        
        // Get assigned staff if present
        $assignedStaff = null;
        if ($notice->assigned_to) {
            $assignedStaff = User::find($notice->assigned_to);
        }
        
        // Get care managers
        $careManagers = User::where('role_id', 2)->get();
        
        // Create notification title based on update type
        $title = match($update->update_type) {
            'response' => 'Response to Emergency Notice',
            'status_change' => 'Emergency Notice Status Updated',
            'assignment' => 'Emergency Notice Assigned',
            'resolution' => 'Emergency Notice Resolved',
            'note' => 'Note Added to Emergency Notice',
            default => 'Emergency Notice Update'
        };
        
        // If status changed, add it to the title
        if ($update->update_type === 'status_change' && $update->status_change_to) {
            $status = match($update->status_change_to) {
                'in_progress' => 'In Progress',
                'resolved' => 'Resolved',
                'archived' => 'Archived',
                default => $update->status_change_to
            };
            $title .= " - Now {$status}";
        }
        
        // Create message content
        $message = "Emergency notice for {$beneficiary->first_name} {$beneficiary->last_name} has been updated by {$actor->first_name} {$actor->last_name}.";
        $message .= "\n\n{$update->message}";
        
        // Notify beneficiary if they have portal access
        if ($beneficiary) {
            Notification::create([
                'user_id' => $beneficiary->beneficiary_id,
                'user_type' => 'beneficiary',
                'message_title' => $title,
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
        }
        
        // Notify family members
        $familyMembers = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)->get();
        foreach ($familyMembers as $familyMember) {
                Notification::create([
                    'user_id' => $familyMember->family_member_id,
                    'user_type' => 'family_member',
                    'message_title' => $title,
                    'message' => $message,
                    'date_created' => now(),
                    'is_read' => false
                ]);
            }
        
        // Notify assigned staff
        if ($assignedStaff && $assignedStaff->id != $actor->id) {
            Notification::create([
                'user_id' => $assignedStaff->id,
                'user_type' => 'cose_staff',
                'message_title' => $title,
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
        }
        
        // Notify care managers (except the one who made the update)
        foreach ($careManagers as $careManager) {
            if ($careManager->id != $actor->id) {
                Notification::create([
                    'user_id' => $careManager->id,
                    'user_type' => 'cose_staff',
                    'message_title' => $title,
                    'message' => $message,
                    'date_created' => now(),
                    'is_read' => false
                ]);
            }
        }
    }

    /**
     * Send notifications to relevant stakeholders about service request updates
     */
    private function sendServiceRequestNotifications(ServiceRequest $request, ServiceRequestUpdate $update, User $actor)
    {
        // Get beneficiary
        $beneficiary = Beneficiary::find($request->beneficiary_id);
        if (!$beneficiary) return;
        
        // Get care worker if assigned
        $careWorker = null;
        if ($request->care_worker_id) {
            $careWorker = User::find($request->care_worker_id);
        }
        
        // Get care managers
        $careManagers = User::where('role_id', 2)->get();
        
        // Create notification title based on update type
        $title = match($update->update_type) {
            'approval' => 'Service Request Approved',
            'rejection' => 'Service Request Rejected',
            'assignment' => 'Care Worker Assigned to Service Request',
            'completion' => 'Service Request Completed',
            'note' => 'Note Added to Service Request',
            default => 'Service Request Update'
        };
        
        // Create message content
        $message = "Service request for {$beneficiary->first_name} {$beneficiary->last_name} has been updated by {$actor->first_name} {$actor->last_name}.";
        $message .= "\n\n{$update->message}";
        
        if ($careWorker && $update->update_type === 'approval') {
            $message .= "\n\nCare Worker assigned: {$careWorker->first_name} {$careWorker->last_name}";
        }
        
        // Notify beneficiary if they have portal access
        if ($beneficiary) {
            Notification::create([
                'user_id' => $beneficiary->beneficiary_id,
                'user_type' => 'beneficiary',
                'message_title' => $title,
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
        }
        
        // Notify family members
        $familyMembers = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)->get();
        foreach ($familyMembers as $familyMember) {
                Notification::create([
                    'user_id' => $familyMember->family_member_id,
                    'user_type' => 'family_member',
                    'message_title' => $title,
                    'message' => $message,
                    'date_created' => now(),
                    'is_read' => false
                ]);
            }
        
        // Notify care worker
        if ($careWorker && $careWorker->id != $actor->id) {
            Notification::create([
                'user_id' => $careWorker->id,
                'user_type' => 'cose_staff',
                'message_title' => $title,
                'message' => $message,
                'date_created' => now(),
                'is_read' => false
            ]);
        }
        
        // Notify care managers (except the one who made the update)
        foreach ($careManagers as $careManager) {
            if ($careManager->id != $actor->id) {
                Notification::create([
                    'user_id' => $careManager->id,
                    'user_type' => 'cose_staff',
                    'message_title' => $title,
                    'message' => $message,
                    'date_created' => now(),
                    'is_read' => false
                ]);
            }
        }
    }

    /**
     * Get a specific emergency notice for modals
     * @param int $id Emergency notice ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmergencyNotice($id)
    {
        try {
            $emergencyNotice = EmergencyNotice::with([
                'beneficiary',
                'beneficiary.barangay',  // Include barangay relation
                'beneficiary.municipality',  // Include municipality relation
                'emergencyType', 
                'sender', 
                'actionTakenBy',
                'assignedUser',
                'updates' => function($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'updates.updatedByUser'
            ])->findOrFail($id);
            
            // Add names for better display
            foreach ($emergencyNotice->updates as $update) {
                $user = $update->updatedByUser;
                $update->updated_by_name = $user ? $user->first_name . ' ' . $user->last_name : 'Unknown';
            }
            
            return response()->json([
                'success' => true,
                'emergency_notice' => $emergencyNotice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Emergency notice not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get a specific service request for modals
     * @param int $id Service request ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServiceRequest($id)
    {
        try {
            $serviceRequest = ServiceRequest::with([
                'beneficiary',
                'beneficiary.barangay',      // Add these relationships explicitly
                'beneficiary.municipality',  // Add these relationships explicitly 
                'serviceType', 
                'sender', 
                'actionTakenBy',
                'careWorker',
                'updates' => function($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'updates.updatedByUser'
            ])->findOrFail($id);
            
            // Add names for better display
            foreach ($serviceRequest->updates as $update) {
                $user = $update->updatedByUser;
                $update->updated_by_name = $user ? $user->first_name . ' ' . $user->last_name : 'Unknown';
            }

            // Add action_taken_by_name
            if ($serviceRequest->actionTakenBy) {
                $serviceRequest->action_taken_by_name = $serviceRequest->actionTakenBy->first_name . ' ' . $serviceRequest->actionTakenBy->last_name;
            }
            
            return response()->json([
                'success' => true,
                'service_request' => $serviceRequest
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getServiceRequest: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Service request not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get all care workers for dropdown lists
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCareWorkers()
    {
        try {
            // Make sure User model is properly imported
            // Add this if it's missing: use App\Models\User;
            
            // Log the start of the function for debugging
            \Log::info('Fetching care workers');
            
            // Get active care workers only - modified query to be more fault-tolerant
            $careWorkers = User::where('role_id', 3)
                ->where(function($query) {
                    $query->where('status', 'Active')
                        ->orWhereNull('status');
                })
                ->select('id', 'first_name', 'last_name')
                ->orderBy('first_name')
                ->get();
            
            // Log success
            \Log::info('Successfully fetched care workers: ' . $careWorkers->count());
            
            return response()->json([
                'success' => true,
                'care_workers' => $careWorkers
            ]);
        } catch (\Exception $e) {
            // Log the specific error
            \Log::error('Failed to fetch care workers: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch care workers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}

