<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Models\EmergencyServiceRequest;
use App\Services\NotificationService;
use App\Services\LogService;

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
     * List active emergency/service requests for the authenticated user.
     */
    public function active(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $activeRequests = EmergencyServiceRequest::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activeRequests
        ]);
    }

    /**
     * List history of emergency/service requests for the authenticated user.
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $history = EmergencyServiceRequest::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('status', '!=', 'active')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Submit a new emergency request.
     */
    public function submitEmergency(Request $request)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        if (!$beneficiary) {
            return response()->json(['success' => false, 'message' => 'Beneficiary not found.'], 404);
        }

        $request->validate([
            'description' => 'required|string|max:1000',
            'location' => 'nullable|string|max:255',
        ]);

        $emergency = EmergencyServiceRequest::create([
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'type' => 'emergency',
            'description' => $request->description,
            'location' => $request->location,
            'status' => 'active',
            'requested_by' => $user->id,
        ]);

        // Notify care team
        $this->notificationService->notifyCareTeam($beneficiary, 'New emergency request submitted.');

        // Log the action
        $this->logService->createLog(
            $user->id,
            'emergency_request_submitted',
            "Emergency request ID {$emergency->id} submitted by user {$user->id}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Emergency request submitted.',
            'data' => $emergency
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
            'description' => 'required|string|max:1000',
            'location' => 'nullable|string|max:255',
        ]);

        $service = EmergencyServiceRequest::create([
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'type' => 'service',
            'description' => $request->description,
            'location' => $request->location,
            'status' => 'active',
            'requested_by' => $user->id,
        ]);

        // Notify care team
        $this->notificationService->notifyCareTeam($beneficiary, 'New service request submitted.');

        // Log the action
        $this->logService->createLog(
            $user->id,
            'service_request_submitted',
            "Service request ID {$service->id} submitted by user {$user->id}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Service request submitted.',
            'data' => $service
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
            'request_id' => 'required|integer|exists:emergency_service_requests,id',
        ]);

        $serviceRequest = EmergencyServiceRequest::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('id', $request->request_id)
            ->where('status', 'active')
            ->first();

        if (!$serviceRequest) {
            return response()->json(['success' => false, 'message' => 'Active request not found.'], 404);
        }

        $serviceRequest->status = 'cancelled';
        $serviceRequest->cancelled_at = now();
        $serviceRequest->save();

        // Log the action
        $this->logService->createLog(
            $user->id,
            'service_request_cancelled',
            "Service request ID {$serviceRequest->id} cancelled by user {$user->id}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Request cancelled.'
        ]);
    }

    /**
     * Get details of a specific emergency request.
     */
    public function emergencyDetails(Request $request, $id)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        $emergency = EmergencyServiceRequest::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('id', $id)
            ->where('type', 'emergency')
            ->first();

        if (!$emergency) {
            return response()->json(['success' => false, 'message' => 'Emergency request not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $emergency
        ]);
    }

    /**
     * Get details of a specific service request.
     */
    public function serviceDetails(Request $request, $id)
    {
        $user = $request->user();
        $beneficiary = $this->getCurrentBeneficiary($user);

        $service = EmergencyServiceRequest::where('beneficiary_id', $beneficiary->beneficiary_id)
            ->where('id', $id)
            ->where('type', 'service')
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
}
