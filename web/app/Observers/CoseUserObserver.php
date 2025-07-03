<?php
namespace App\Observers;

use App\Models\User; // This is for cose_users
use App\Models\UnifiedUser; // This is for the unified users table
use Illuminate\Support\Facades\Log;

class CoseUserObserver
{
    public function created(User $coseUser)
    {
        // Prevent duplicate UnifiedUser for this cose user
        $existing = UnifiedUser::where('cose_user_id', $coseUser->id)->first();
        $data = [
            'email' => $coseUser->email,
            'password' => $coseUser->password,
            'first_name' => $coseUser->first_name,
            'last_name' => $coseUser->last_name,
            'mobile' => $coseUser->mobile,
            'role_id' => $coseUser->role_id,
            'status' => $coseUser->status,
            'user_type' => 'cose',
            'cose_user_id' => $coseUser->id,
            'beneficiary_id' => null,
            'family_member_id' => null,
            'username' => null,
        ];

        try {
            if ($existing) {
                $existing->update($data);
            } else {
                UnifiedUser::create($data);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to sync UnifiedUser for CoseUser (create): ' . $e->getMessage());
        }
    }

    public function updated(User $coseUser)
    {
        $unifiedUser = UnifiedUser::where('cose_user_id', $coseUser->id)->first();
        $data = [
            'email' => $coseUser->email,
            'password' => $coseUser->password,
            'first_name' => $coseUser->first_name,
            'last_name' => $coseUser->last_name,
            'mobile' => $coseUser->mobile,
            'role_id' => $coseUser->role_id,
            'status' => $coseUser->status,
            'username' => null,
            'beneficiary_id' => null,
            'family_member_id' => null,
        ];

        try {
            if ($unifiedUser) {
                $unifiedUser->update($data);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to sync UnifiedUser for CoseUser (update): ' . $e->getMessage());
        }
    }

    public function deleted(User $coseUser)
    {
        try {
            UnifiedUser::where('cose_user_id', $coseUser->id)->delete();
        } catch (\Throwable $e) {
            Log::error('Failed to delete UnifiedUser for CoseUser (delete): ' . $e->getMessage());
        }
    }
}