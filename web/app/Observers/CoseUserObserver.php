<?php
namespace App\Observers;

use App\Models\User; // This is for cose_users
use App\Models\UnifiedUser; // This is for the unified users table

class CoseUserObserver
{
    public function created(User $coseUser)
    {
        UnifiedUser::create([
            'email' => $coseUser->email,
            'password' => $coseUser->password,
            'first_name' => $coseUser->first_name,
            'last_name' => $coseUser->last_name,
            'mobile' => $coseUser->mobile,
            'role_id' => $coseUser->role_id,
            'status' => $coseUser->status,
            'user_type' => 'cose',
            'cose_user_id' => $coseUser->id,
        ]);
    }

    public function updated(User $coseUser)
    {
        $unifiedUser = UnifiedUser::where('cose_user_id', $coseUser->id)->first();
        if ($unifiedUser) {
            $unifiedUser->update([
                'email' => $coseUser->email,
                'first_name' => $coseUser->first_name,
                'last_name' => $coseUser->last_name,
                'mobile' => $coseUser->mobile,
                'role_id' => $coseUser->role_id,
                'status' => $coseUser->status,
            ]);
        }
    }

    public function deleted(User $coseUser)
    {
        UnifiedUser::where('cose_user_id', $coseUser->id)->delete();
    }
}