<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FcmApiController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Register or update FCM token for authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    // Validate Expo push token format
                    if (!$this->isValidExpoPushToken($value)) {
                        $fail('The token must be a valid Expo push token.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $token = $request->input('token');

            // Determine user role and ID based on role_id
            [$userId, $role] = $this->getUserIdAndRole($user);

            // Register the token (this will replace any existing tokens for the user)
            $fcmToken = $this->notificationService->register($userId, $role, $token);

            return response()->json([
                'success' => true,
                'message' => 'FCM token registered successfully',
                'data' => [
                    'id' => $fcmToken->id,
                    'token' => $fcmToken->token,
                    'role' => $fcmToken->role,
                    'registered_at' => $fcmToken->created_at
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register FCM token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get FCM token for authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Determine user role and ID based on role_id
            [$userId, $role] = $this->getUserIdAndRole($user);

            $fcmToken = $this->notificationService->getTokenByUser($userId, $role);

            if (!$fcmToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'No FCM token found for user'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $fcmToken->id,
                    'token' => $fcmToken->token,
                    'role' => $fcmToken->role,
                    'registered_at' => $fcmToken->created_at
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve FCM token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove FCM token for authenticated user and device (revoke)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function revoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            // COMMENTED OUT IF NO MOBILE DEVICES TABLE USAGE
            // 'device_uuid' => 'nullable|string',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails() || (!$request->filled('device_uuid') && !$request->filled('fcm_token'))) {
            return response()->json([
                'success' => false,
                // COMMENTED OUT IF NO MOBILE DEVICES TABLE USAGE
                // 'message' => 'Validation failed. Provide either device_uuid or fcm_token.',
                'message' => 'Fail.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            [$userId, $role] = $this->getUserIdAndRole($user);

            $deleted = false;

            // Revoke by fcm_token
            if ($request->filled('fcm_token')) {
                $fcmToken = FcmToken::where('user_id', $userId)
                    ->where('role', $role)
                    ->where('token', $request->input('fcm_token'))
                    ->first();

                // COMMENTED OUT IF NO MOBILE DEVICES TABLE USAGE
                // if ($fcmToken) {
                //     $mobileDeviceId = $fcmToken->mobile_device_id;
                //     $fcmToken->delete();
                //     // Always delete the device entry
                //     MobileDevice::where('id', $mobileDeviceId)
                //         ->where('user_id', $userId)
                //         ->where('user_type', $role)
                //         ->delete();
                //     $deleted = true;
                // }

                // IF COMMENTED OUT, DELETE ONLY FCM TOKEN
                $fcmToken->delete();
            }

            // // Revoke by device_uuid
            // if (!$deleted && $request->filled('device_uuid')) {
            //     $mobileDevice = MobileDevice::where('device_uuid', $request->input('device_uuid'))
            //         ->where('user_id', $userId)
            //         ->where('user_type', $role)
            //         ->first();

            //     if ($mobileDevice) {
            //         FcmToken::where('user_id', $userId)
            //             ->where('role', $role)
            //             ->where('mobile_device_id', $mobileDevice->id)
            //             ->delete();

            //         $mobileDevice->delete();
            //         $deleted = true;
            //     }
            // }

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'FCM token and device unregistered successfully'
                ], 200);
            } 
            // else {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'No matching FCM token or device found'
            //     ], 404);
            // }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unregister FCM token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user ID and role based on the authenticated user's role_id
     *
     * @param mixed $user
     * @return array [userId, role]
     */
    private function getUserIdAndRole($user): array
    {
        switch ($user->role_id) {
            case 4: // Beneficiary
                return [$user->beneficiary_id, 'beneficiary'];
            case 5: // Family Member
                return [$user->family_member_id, 'family_member'];
            case 1: // Admin
            case 2: // Care Manager
            case 3: // Care Worker
                return [$user->id, 'cose_staff'];
            default:
                throw new \InvalidArgumentException('Invalid user role');
        }
    }

    /**
     * Validate Expo push token format
     *
     * @param string $token
     * @return bool
     */
    private function isValidExpoPushToken(string $token): bool
    {
        // Expo push tokens typically start with "ExponentPushToken[" or "ExpoPushToken["
        // and have a specific format
        $pattern = '/^(ExponentPushToken|ExpoPushToken)\[[\w-]+\]$/';
        return preg_match($pattern, $token) === 1;
    }
}
