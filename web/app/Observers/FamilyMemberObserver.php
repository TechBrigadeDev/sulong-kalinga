<?php

namespace App\Observers;

use App\Models\FamilyMember;
use App\Models\Beneficiary;
use App\Models\UnifiedUser;
use Illuminate\Support\Facades\Log;

class FamilyMemberObserver
{
    public function created(FamilyMember $member)
    {
        // Prevent duplicate UnifiedUser for this family member
        $existing = UnifiedUser::where('family_member_id', $member->family_member_id)->first();
        $data = [
            'email' => $member->email,
            'password' => $member->password,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'mobile' => $member->mobile,
            'role_id' => 5,
            // Set status based on related beneficiary's status
            'status' => $this->getStatusFromBeneficiary($member->related_beneficiary_id),
            'user_type' => 'family_member',
            'cose_user_id' => null,
            'beneficiary_id' => null,
            'username' => null, // Not used for family members
            'family_member_id' => $member->family_member_id,
        ];

        try {
            if ($existing) {
                $existing->update($data);
            } else {
                UnifiedUser::create($data);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to sync UnifiedUser for FamilyMember (create): ' . $e->getMessage());
        }
    }

    public function updated(FamilyMember $member)
    {
        $unifiedUser = UnifiedUser::where('family_member_id', $member->family_member_id)->first();
        $data = [
            'email' => $member->email,
            'password' => $member->password,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'mobile' => $member->mobile,
            // Set status based on related beneficiary's status
            'status' => $this->getStatusFromBeneficiary($member->related_beneficiary_id),
            'username' => null,
            'cose_user_id' => null,
            'beneficiary_id' => null,
        ];

        try {
            if ($unifiedUser) {
                $unifiedUser->update($data);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to sync UnifiedUser for FamilyMember (update): ' . $e->getMessage());
        }
    }

    public function deleted(FamilyMember $member)
    {
        try {
            UnifiedUser::where('family_member_id', $member->family_member_id)->delete();
        } catch (\Throwable $e) {
            Log::error('Failed to delete UnifiedUser for FamilyMember (delete): ' . $e->getMessage());
        }
    }

    /**
     * Listen for beneficiary status changes and update related family members' UnifiedUser status.
     */
    public static function beneficiaryStatusChanged(Beneficiary $beneficiary)
    {
        $status = $beneficiary->beneficiary_status_id == 1 ? 'Active' : 'Inactive';
        $familyMembers = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)->get();

        foreach ($familyMembers as $member) {
            $unifiedUser = UnifiedUser::where('family_member_id', $member->family_member_id)->first();
            if ($unifiedUser) {
                $unifiedUser->status = $status;
                $unifiedUser->save();
            }
        }
    }

    /**
     * Helper to get status from beneficiary
     */
    private function getStatusFromBeneficiary($beneficiaryId)
    {
        $beneficiary = Beneficiary::find($beneficiaryId);
        return ($beneficiary && $beneficiary->beneficiary_status_id == 1) ? 'Active' : 'Inactive';
    }
}