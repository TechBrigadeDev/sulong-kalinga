<?php

namespace App\Observers;

use App\Models\PortalAccount;
use App\Models\UnifiedUser;

class PortalAccountObserver
{
    public function created(PortalAccount $portalAccount)
    {
        // Prevent duplicate unified users for the same email
        if (UnifiedUser::where('email', $portalAccount->portal_email)->exists()) {
            return;
        }

        UnifiedUser::create([
            'email' => $portalAccount->portal_email,
            'password' => $portalAccount->portal_password,
            'role_id' => 4, // Portal user
            'status' => 'Active',
            'user_type' => 'portal',
            'portal_account_id' => $portalAccount->id,
        ]);
    }

    public function deleted(PortalAccount $portalAccount)
    {
        UnifiedUser::where('portal_account_id', $portalAccount->id)->delete();
    }
}