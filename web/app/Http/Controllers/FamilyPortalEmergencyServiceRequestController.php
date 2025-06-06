<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmergencyNotice;
use App\Models\ServiceRequest;
use App\Models\EmergencyType;
use App\Models\ServiceRequestType;
use App\Models\EmergencyUpdate;
use App\Models\ServiceRequestUpdate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FamilyPortalEmergencyServiceRequestController extends Controller
{
    public function index()
    {
        try {
            // Determine whether the user is a beneficiary or family member
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            
            // Get user details
            if ($userType === 'beneficiary') {
                $user = Auth::guard('beneficiary')->user();
                $beneficiaryId = $user->beneficiary_id;
            } else {
                $user = Auth::guard('family')->user();
                $beneficiaryId = $user->related_beneficiary_id;
            }
            
            // Fetch service types and emergency types
            $serviceTypes = ServiceRequestType::orderBy('name')->get();
            $emergencyTypes = EmergencyType::orderBy('name')->get();
            
            // Get active requests
            $activeEmergencies = EmergencyNotice::with(['emergencyType', 'assignedUser'])
                ->where('beneficiary_id', $beneficiaryId)
                ->whereIn('status', ['new', 'in_progress'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            $activeServiceRequests = ServiceRequest::with(['serviceType', 'careWorker'])
                ->where('beneficiary_id', $beneficiaryId)
                ->whereIn('status', ['new', 'approved'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            // Get request history
            $emergencyHistory = EmergencyNotice::with(['emergencyType', 'assignedUser'])
                ->where('beneficiary_id', $beneficiaryId)
                ->whereIn('status', ['resolved', 'archived'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();
                
            $serviceRequestHistory = ServiceRequest::with(['serviceType', 'careWorker'])
                ->where('beneficiary_id', $beneficiaryId)
                ->whereIn('status', ['completed', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();
            
            $viewName = $userType === 'beneficiary' ? 'beneficiaryPortal.emergencyAndService' : 'familyPortal.emergencyAndService';
            
            return view($viewName, compact(
                'serviceTypes',
                'emergencyTypes',
                'activeEmergencies',
                'activeServiceRequests',
                'emergencyHistory',
                'serviceRequestHistory'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in emergency service index: ' . $e->getMessage());
            
            // Return with error
            return back()->with('error', 'An error occurred while loading the page. Please try again.');
        }
    }
    
    /**
     * Submit a new emergency request
     */
    public function submitEmergencyRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emergency_type_id' => 'required|exists:emergency_types,emergency_type_id',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Determine whether the user is a beneficiary or family member
        $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
        
        if ($userType === 'beneficiary') {
            $user = Auth::guard('beneficiary')->user();
            $senderId = $user->beneficiary_id;
            $beneficiaryId = $user->beneficiary_id;
        } else {
            $user = Auth::guard('family')->user();
            $senderId = $user->family_member_id;
            $beneficiaryId = $user->related_beneficiary_id;
        }
        
        try {
            // Create the emergency notice
            $emergencyNotice = EmergencyNotice::create([
                'sender_id' => $senderId,
                'sender_type' => $userType,
                'beneficiary_id' => $beneficiaryId,
                'emergency_type_id' => $request->emergency_type_id,
                'message' => $request->message,
                'status' => 'new',
                'read_status' => false
            ]);
            
            // Return the created emergency notice with its type
            $emergencyNotice->load('emergencyType');
            
            return response()->json([
                'success' => true,
                'message' => 'Emergency assistance request submitted successfully.',
                'data' => $emergencyNotice
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error submitting emergency request: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit emergency request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Submit a new service request
     */
    public function submitServiceRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type_id' => 'required|exists:service_request_types,service_type_id',
            'service_date' => 'required|date|after_or_equal:today',
            'service_time' => 'required',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Determine whether the user is a beneficiary or family member
        $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
        
        if ($userType === 'beneficiary') {
            $user = Auth::guard('beneficiary')->user();
            $senderId = $user->beneficiary_id;
            $beneficiaryId = $user->beneficiary_id;
        } else {
            $user = Auth::guard('family')->user();
            $senderId = $user->family_member_id;
            $beneficiaryId = $user->related_beneficiary_id;
        }
        
        try {
            // Create the service request
            $serviceRequest = ServiceRequest::create([
                'sender_id' => $senderId,
                'sender_type' => $userType,
                'beneficiary_id' => $beneficiaryId,
                'service_type_id' => $request->service_type_id,
                'service_date' => $request->service_date,
                'service_time' => $request->service_time,
                'message' => $request->message,
                'status' => 'new',
                'read_status' => false
            ]);
            
            // Return the created service request with its type
            $serviceRequest->load('serviceType');
            
            return response()->json([
                'success' => true,
                'message' => 'Service request submitted successfully.',
                'data' => $serviceRequest
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error submitting service request: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit service request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get active requests for AJAX updates
     */
    public function getActiveRequests()
    {
        try {
            // Determine whether the user is a beneficiary or family member
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            
            if ($userType === 'beneficiary') {
                $user = Auth::guard('beneficiary')->user();
                $beneficiaryId = $user->beneficiary_id;
            } else {
                $user = Auth::guard('family')->user();
                $beneficiaryId = $user->related_beneficiary_id;
            }
            
            $activeEmergencies = EmergencyNotice::with(['emergencyType', 'assignedUser'])
                ->where('beneficiary_id', $beneficiaryId)
                ->whereIn('status', ['new', 'in_progress'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            $activeServiceRequests = ServiceRequest::with(['serviceType', 'careWorker'])
                ->where('beneficiary_id', $beneficiaryId)
                ->whereIn('status', ['new', 'approved'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json([
                'success' => true,
                'emergencies' => $activeEmergencies,
                'serviceRequests' => $activeServiceRequests
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting active requests: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get active requests.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get request history filtered by parameters
     */
    public function getRequestHistory(Request $request)
    {
        try {
            // Determine whether the user is a beneficiary or family member
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            
            if ($userType === 'beneficiary') {
                $user = Auth::guard('beneficiary')->user();
                $beneficiaryId = $user->beneficiary_id;
            } else {
                $user = Auth::guard('family')->user();
                $beneficiaryId = $user->related_beneficiary_id;
            }
            
            // Get filters
            $includeEmergency = $request->input('include_emergency', true);
            $includeService = $request->input('include_service', true);
            $includeCompleted = $request->input('include_completed', true);
            $includeRejected = $request->input('include_rejected', true);
            $dateRange = $request->input('date_range', 'all');
            
            // Calculate date range
            $startDate = null;
            $endDate = Carbon::now();
            
            switch ($dateRange) {
                case 'today':
                    $startDate = Carbon::today();
                    break;
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    break;
                case 'custom':
                    $startDate = $request->input('start_date') 
                        ? Carbon::parse($request->input('start_date')) 
                        : Carbon::now()->subYear();
                    $endDate = $request->input('end_date') 
                        ? Carbon::parse($request->input('end_date')) 
                        : Carbon::now();
                    break;
                default:
                    $startDate = Carbon::now()->subYears(5); // Default: show last 5 years
            }
            
            $result = [];
            
            // Get filtered emergency history if requested
            if ($includeEmergency) {
                $emergencyQuery = EmergencyNotice::with(['emergencyType', 'assignedUser'])
                    ->where('beneficiary_id', $beneficiaryId)
                    ->whereBetween('updated_at', [$startDate, $endDate]);
                    
                $statuses = [];
                if ($includeCompleted) $statuses[] = 'resolved';
                if ($includeRejected) $statuses[] = 'archived';
                
                if (!empty($statuses)) {
                    $emergencyQuery->whereIn('status', $statuses);
                }
                
                $emergencyHistory = $emergencyQuery->orderBy('updated_at', 'desc')->get();
                $result['emergencies'] = $emergencyHistory;
            }
            
            // Get filtered service request history if requested
            if ($includeService) {
                $serviceQuery = ServiceRequest::with(['serviceType', 'careWorker'])
                    ->where('beneficiary_id', $beneficiaryId)
                    ->whereBetween('updated_at', [$startDate, $endDate]);
                    
                $statuses = [];
                if ($includeCompleted) $statuses[] = 'completed';
                if ($includeRejected) $statuses[] = 'rejected';
                
                if (!empty($statuses)) {
                    $serviceQuery->whereIn('status', $statuses);
                }
                
                $serviceHistory = $serviceQuery->orderBy('updated_at', 'desc')->get();
                $result['serviceRequests'] = $serviceHistory;
            }
            
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting request history: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get request history.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cancel an active request if allowed (new status only)
     */
    public function cancelRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|integer',
            'request_type' => 'required|in:emergency,service'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Determine whether the user is a beneficiary or family member
            $userType = Auth::guard('beneficiary')->check() ? 'beneficiary' : 'family';
            
            if ($userType === 'beneficiary') {
                $user = Auth::guard('beneficiary')->user();
                $beneficiaryId = $user->beneficiary_id;
            } else {
                $user = Auth::guard('family')->user();
                $beneficiaryId = $user->related_beneficiary_id;
            }
            
            $requestId = $request->request_id;
            $requestType = $request->request_type;
            
            if ($requestType === 'emergency') {
                $emergencyRequest = EmergencyNotice::where('notice_id', $requestId)
                    ->where('beneficiary_id', $beneficiaryId)
                    ->where('status', 'new') // Can only cancel new requests
                    ->first();
                    
                if (!$emergencyRequest) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Emergency request not found or cannot be cancelled.'
                    ], 404);
                }
                
                // Mark as archived (cancelled)
                $emergencyRequest->status = 'archived';
                $emergencyRequest->save();
                
            } else { // service
                $serviceRequest = ServiceRequest::where('service_request_id', $requestId)
                    ->where('beneficiary_id', $beneficiaryId)
                    ->where('status', 'new') // Can only cancel new requests
                    ->first();
                    
                if (!$serviceRequest) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Service request not found or cannot be cancelled.'
                    ], 404);
                }
                
                // Mark as rejected (cancelled)
                $serviceRequest->status = 'rejected';
                $serviceRequest->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($requestType) . ' request cancelled successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error cancelling request: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}