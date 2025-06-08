<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\EmergencyNotice;
use App\Models\ServiceRequest;
use App\Models\EmergencyType;
use App\Models\ServiceRequestType;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\LogService;
use Carbon\Carbon;

class EmergencyServiceRequestApiController extends Controller
{
    protected $notificationService;
    protected $logService;

    public function __construct(NotificationService $notificationService, LogService $logService)
    {
        $this->notificationService = $notificationService;
        $this->logService = $logService;
    }

    /**
     * List active emergency and service requests for the authenticated user.
     */
    public function active(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $emergencies = collect(EmergencyNotice::with(['emergencyType', 'assignedUser'])
            ->where('beneficiary_id', $beneficiary->beneficiary_id)
            ->whereIn('status', ['new', 'in_progress'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'emergency',
                    'id' => $item->notice_id,
                    'description' => $item->message,
                    'date_submitted' => $item->created_at,
                    'status' => $item->status,
                    'assigned_to' => $item->assignedUser ? $item->assignedUser->first_name . ' ' . $item->assignedUser->last_name : null,
                    'actions' => $this->getActions($item->status, $item->assignedUser),
                ];
            }));

        $services = collect(ServiceRequest::with(['serviceType', 'careWorker'])
            ->where('beneficiary_id', $beneficiary->beneficiary_id)
            ->whereIn('status', ['new', 'approved'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'service',
                    'id' => $item->service_request_id,
                    'description' => $item->message,
                    'date_submitted' => $item->created_at,
                    'status' => $item->status,
                    'assigned_to' => $item->careWorker ? $item->careWorker->first_name . ' ' . $item->careWorker->last_name : null,
                    'actions' => $this->getActions($item->status, $item->careWorker),
                ];
            }));

        return response()->json([
            'success' => true,
            'data' => $emergencies->merge($services)->sortByDesc('date_submitted')->values()
        ]);
    }

    /**
     * List history of emergency and service requests for the authenticated user.
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $emergencies = EmergencyNotice::with(['emergencyType', 'assignedUser'])
            ->where('beneficiary_id', $beneficiary->beneficiary_id)
            ->whereIn('status', ['resolved', 'archived'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'emergency',
                    'id' => $item->notice_id,
                    'description' => $item->message,
                    'date_submitted' => $item->created_at,
                    'status' => $item->status,
                    'assigned_to' => $item->assignedUser ? $item->assignedUser->first_name . ' ' . $item->assignedUser->last_name : null,
                    'actions' => $this->getActions($item->status, $item->assignedUser),
                ];
            });

        $services = ServiceRequest::with(['serviceType', 'careWorker'])
            ->where('beneficiary_id', $beneficiary->beneficiary_id)
            ->whereIn('status', ['rejected', 'completed'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'service',
                    'id' => $item->service_request_id,
                    'description' => $item->message,
                    'date_submitted' => $item->created_at,
                    'status' => $item->status,
                    'assigned_to' => $item->careWorker ? $item->careWorker->first_name . ' ' . $item->careWorker->last_name : null,
                    'actions' => $this->getActions($item->status, $item->careWorker),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $emergencies->merge($services)->sortByDesc('date_submitted')->values()
        ]);
    }

    /**
     * Submit a new service request.
     */
    public function submitService(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $request->validate([
            'service_type_id' => 'required|exists:service_request_types,service_type_id',
            'service_date' => 'required|date|after:today',
            'service_time' => 'required|date_format:H:i',
            'message' => 'required|string|max:1000',
        ], [
            'service_date.after' => 'The preferred date must be after today.'
        ]);

        $service = ServiceRequest::create([
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'service_type_id' => $request->service_type_id,
            'service_date' => $request->service_date,
            'service_time' => $request->service_time,
            'message' => $request->message,
            'status' => 'new',
            'read_status' => false,
            'read_at' => null,
            'sender_id' => $user->id,
            'sender_type' => $user->role_id == 4 ? 'beneficiary' : 'family_member',
        ]);

        // Notify assigned care worker and care manager only
        if ($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id) {
            $careWorkerId = $beneficiary->generalCarePlan->care_worker_id;
            $this->notificationService->notifyStaff(
                $careWorkerId,
                'New service request submitted',
                'A new service request has been submitted by a beneficiary or family member.'
            );

            $careWorker = User::find($careWorkerId);
            if ($careWorker && $careWorker->care_manager_id) {
                $this->notificationService->notifyStaff(
                    $careWorker->care_manager_id,
                    'New service request submitted',
                    'A new service request has been submitted by a beneficiary or family member.'
                );
            }
        }

        $this->logService->createLog(
            'service_request',
            $service->service_request_id,
            'service_request_submitted',
            "Service request ID {$service->service_request_id} submitted by user {$user->id}",
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Service request submitted.',
            'data' => $service
        ]);
    }

    /**
     * Update a service request (mobile only).
     */
    public function updateService(Request $request, $id)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        $service = ServiceRequest::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('service_request_id', $id)
            ->where('status', 'new')
            ->first();

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'You can only update a service request with status new.'], 404);
        }

        $request->validate([
            'service_type_id' => 'sometimes|exists:service_request_types,service_type_id',
            'service_date' => 'sometimes|date|after:today',
            'service_time' => 'sometimes|date_format:H:i',
            'message' => 'sometimes|string|max:1000',
        ], [
            'service_date.after' => 'The preferred date must be after today.'
        ]);

        if ($request->has('service_type_id')) $service->service_type_id = $request->service_type_id;
        if ($request->has('service_date')) $service->service_date = $request->service_date;
        if ($request->has('service_time')) $service->service_time = $request->service_time;
        if ($request->has('message')) $service->message = $request->message;
        $service->read_status = false;
        $service->read_at = null;
        $service->save();

        $this->logService->createLog(
            'service_request',
            $service->service_request_id,
            'service_request_updated',
            "Service request ID {$service->service_request_id} updated by user {$user->id}",
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Service request updated.',
            'data' => $service
        ]);
    }

    /**
     * Submit a new emergency notice.
     */
    public function submitEmergency(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $request->validate([
            'emergency_type_id' => 'required|exists:emergency_types,emergency_type_id',
            'message' => 'required|string|max:1000',
        ]);

        $emergency = EmergencyNotice::create([
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'emergency_type_id' => $request->emergency_type_id,
            'message' => $request->message,
            'status' => 'new',
            'read_status' => false,
            'read_at' => null,
            'sender_id' => $user->id,
            'sender_type' => $user->role_id == 4 ? 'beneficiary' : 'family_member',
        ]);

        // Notify assigned care worker and care manager only
        if ($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->care_worker_id) {
            $careWorkerId = $beneficiary->generalCarePlan->care_worker_id;
            $this->notificationService->notifyStaff(
                $careWorkerId,
                'New emergency notice submitted',
                'A new emergency notice has been submitted by a beneficiary or family member.'
            );

            // Notify assigned care manager if available
            $careWorker = User::find($careWorkerId);
            if ($careWorker && $careWorker->care_manager_id) {
                $this->notificationService->notifyStaff(
                    $careWorker->care_manager_id,
                    'New emergency notice submitted',
                    'A new emergency notice has been submitted by a beneficiary or family member.'
                );
            }
        }

        $this->logService->createLog(
            'emergency_notice',
            $emergency->notice_id,
            'emergency_notice_submitted',
            "Emergency notice ID {$emergency->notice_id} submitted by user {$user->id}",
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Emergency notice submitted.',
            'data' => $emergency
        ]);
    }

    /**
     * Update an emergency notice (mobile only).
     */
    public function updateEmergency(Request $request, $id)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        $emergency = EmergencyNotice::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('notice_id', $id)
            ->where('status', 'new')
            ->first();

        if (!$emergency) {
            return response()->json(['success' => false, 'message' => 'You can only update an emergency notice with status new.'], 404);
        }

        $request->validate([
            'emergency_type_id' => 'sometimes|exists:emergency_types,emergency_type_id',
            'message' => 'sometimes|string|max:1000',
        ]);

        if ($request->has('emergency_type_id')) $emergency->emergency_type_id = $request->emergency_type_id;
        if ($request->has('message')) $emergency->message = $request->message;
        $emergency->read_status = false;
        $emergency->read_at = null;
        $emergency->save();

        $this->logService->createLog(
            'emergency_notice',
            $emergency->notice_id,
            'emergency_notice_updated',
            "Emergency notice ID {$emergency->notice_id} updated by user {$user->id}",
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Emergency notice updated.',
            'data' => $emergency
        ]);
    }

    /**
     * Delete an emergency notice (mobile only).
     */
    public function deleteEmergency(Request $request, $id)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        $emergency = EmergencyNotice::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('notice_id', $id)
            ->where('status', 'new')
            ->first();

        if (!$emergency) {
            return response()->json(['success' => false, 'message' => 'You can only delete an emergency notice with status new.'], 404);
        }

        $emergency->delete();

        $this->logService->createLog(
            'emergency_notice',
            $id,
            'emergency_notice_deleted',
            "Emergency notice ID {$id} deleted by user {$user->id}",
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Emergency notice deleted.'
        ]);
    }

    /**
     * Delete a service request (mobile only).
     */
    public function deleteService(Request $request, $id)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        $service = ServiceRequest::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('service_request_id', $id)
            ->where('status', 'new')
            ->first();

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'You can only delete a service request with status new.'], 404);
        }

        $service->delete();

        $this->logService->createLog(
            'service_request',
            $id,
            'service_request_deleted',
            "Service request ID {$id} deleted by user {$user->id}",
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Service request deleted.'
        ]);
    }

    /**
     * Cancel an active emergency or service request.
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        $request->validate([
            'type' => 'required|in:emergency,service',
            'id' => 'required|integer',
        ]);

        if ($request->type === 'emergency') {
            $record = EmergencyNotice::where('beneficiary_id', $beneficiary->beneficiary_id)
                ->where('notice_id', $request->id)
                ->where('status', 'new')
                ->first();
            $entityType = 'emergency_notice';
        } else {
            $record = ServiceRequest::where('beneficiary_id', $beneficiary->beneficiary_id)
                ->where('service_request_id', $request->id)
                ->where('status', 'new')
                ->first();
            $entityType = 'service_request';
        }

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'You can only cancel a request with status new.'], 404);
        }

        if ($request->type === 'emergency') {
            $record->status = 'archived';
        } else {
            $record->status = 'rejected';
        }
        $record->read_status = false;
        $record->read_at = null;
        $record->updated_at = now();
        $record->save();

        $this->logService->createLog(
            $entityType,
            $request->id,
            'request_cancelled',
            ucfirst($request->type) . " request ID {$request->id} cancelled by user {$user->id}",
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Request cancelled.'
        ]);
    }

    /**
     * Get details of a specific emergency notice (with COSE notes filtered out).
     */
    public function emergencyDetails(Request $request, $id)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        $emergency = EmergencyNotice::with([
                'emergencyType',
                'updates' => function ($query) {
                    $query->where('update_type', '!=', 'note')->orderBy('created_at', 'desc');
                }
            ])
            ->where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('notice_id', $id)
            ->first();

        if (!$emergency) {
            return response()->json(['success' => false, 'message' => 'Emergency notice not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $emergency
        ]);
    }

    /**
     * Get details of a specific service request (with COSE notes filtered out).
     */
    public function serviceDetails(Request $request, $id)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        $service = ServiceRequest::with([
                'serviceType',
                'updates' => function ($query) {
                    $query->where('update_type', '!=', 'note')->orderBy('created_at', 'desc');
                }
            ])
            ->where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('service_request_id', $id)
            ->first();

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service request not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    /**
     * Helper to get the beneficiary for the current user (beneficiary or family member).
     */
    protected function getCurrentBeneficiary($user)
    {
        if ($user->role_id == 4) {
            return Beneficiary::find($user->beneficiary_id);
        } elseif ($user->role_id == 5) {
            $familyMember = FamilyMember::find($user->family_member_id);
            return $familyMember ? Beneficiary::find($familyMember->related_beneficiary_id) : null;
        }
        return null;
    }

    // Helper for actions (customize as needed)
    private function getActions($status, $assignedUser)
    {
        $actions = [];
        if ($status === 'new') {
            $actions[] = 'cancel';
            if (!$assignedUser) {
                $actions[] = 'edit';
                $actions[] = 'delete';
            }
        }
        return $actions;
    }
}
