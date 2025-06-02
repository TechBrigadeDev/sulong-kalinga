<?php

namespace App\Observers;

use App\Models\FamilyMember;
use App\Models\UnifiedUser;

class FamilyMemberObserver
{
    public function created(FamilyMember $member)
    {
        UnifiedUser::create([
            'email' => $member->email,
            'password' => $member->password,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'mobile' => $member->mobile,
            'role_id' => 6, // or whatever role_id you use for family members
            'status' => null,
            'user_type' => 'family_member',
            'cose_user_id' => null,
            'portal_account_id' => null,
            'username' => null, // Not used for family members
            'family_member_id' => $member->family_member_id,
        ]);
    }

    public function updated(FamilyMember $member)
    {
        $unifiedUser = UnifiedUser::where('family_member_id', $member->family_member_id)->first();
        if ($unifiedUser) {
            $unifiedUser->update([
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'mobile' => $member->mobile,
                'email' => $member->email,
                'password' => $member->password,
                'username' => null, // Not used for family members
            ]);
        }
    }

    public function deleted(FamilyMember $member)
    {
        UnifiedUser::where('family_member_id', $member->family_member_id)->delete();
    }

    // No username generation or password hashing here
}