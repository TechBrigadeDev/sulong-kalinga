<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\MobileDevice;
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
     * Register or update FCM token for authenticated user and device
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
            'device_uuid' => 'required|string',
            'device_type' => 'required|string',
            'device_model' => 'nullable|string',
            'os_version' => 'nullable|string',
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
            $deviceUuid = $request->input('device_uuid');
            $deviceType = $request->input('device_type');
            $deviceModel = $request->input('device_model');
            $osVersion = $request->input('os_version');

            // Determine user role and ID based on role_id
            [$userId, $role] = $this->getUserIdAndRole($user);

            // Register or update the device in mobile_devices
            $mobileDevice = MobileDevice::updateOrCreate(
                ['device_uuid' => $deviceUuid],
                [
                    'user_id' => $userId,
                    'user_type' => $role,
                    'device_type' => $deviceType,
                    'device_model' => $deviceModel,
                    'os_version' => $osVersion,
                ]
            );

            // Register or update the token in fcm_tokens for this user+role+device
            $fcmToken = FcmToken::updateOrCreate(
                [
                    'user_id' => $userId,
                    'role' => $role,
                    'mobile_device_id' => $mobileDevice->id,
                ],
                [
                    'token' => $token,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'FCM token registered successfully',
                'data' => [
                    'id' => $fcmToken->id,
                    'token' => $fcmToken->token,
                    'role' => $fcmToken->role,
                    'mobile_device_id' => $fcmToken->mobile_device_id,
                    'device_uuid' => $mobileDevice->device_uuid,
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
     * Remove FCM token for authenticated user and device (revoke)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function revoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_uuid' => 'nullable|string',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails() || (!$request->filled('device_uuid') && !$request->filled('fcm_token'))) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Provide either device_uuid or fcm_token.',
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

                if ($fcmToken) {
                    $mobileDeviceId = $fcmToken->mobile_device_id;
                    $fcmToken->delete();
                    // Always delete the device entry
                    MobileDevice::where('id', $mobileDeviceId)
                        ->where('user_id', $userId)
                        ->where('user_type', $role)
                        ->delete();
                    $deleted = true;
                }
            }

            // Revoke by device_uuid
            if (!$deleted && $request->filled('device_uuid')) {
                $mobileDevice = MobileDevice::where('device_uuid', $request->input('device_uuid'))
                    ->where('user_id', $userId)
                    ->where('user_type', $role)
                    ->first();

                if ($mobileDevice) {
                    FcmToken::where('user_id', $userId)
                        ->where('role', $role)
                        ->where('mobile_device_id', $mobileDevice->id)
                        ->delete();

                    $mobileDevice->delete();
                    $deleted = true;
                }
            }

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'FCM token and device unregistered successfully'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No matching FCM token or device found'
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unregister FCM token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get FCM tokens for authenticated user (all devices)
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

            $fcmTokens = FcmToken::where('user_id', $userId)
                ->where('role', $role)
                ->with('device')
                ->get();

            if ($fcmTokens->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No FCM tokens found for user'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $fcmTokens->map(function ($token) {
                    return [
                        'id' => $token->id,
                        'token' => $token->token,
                        'role' => $token->role,
                        'mobile_device_id' => $token->mobile_device_id,
                        'device_uuid' => $token->device ? $token->device->device_uuid : null,
                        'device_type' => $token->device ? $token->device->device_type : null,
                        'device_model' => $token->device ? $token->device->device_model : null,
                        'os_version' => $token->device ? $token->device->os_version : null,
                        'registered_at' => $token->created_at
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve FCM tokens',
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
