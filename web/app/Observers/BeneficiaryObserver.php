<?php

namespace App\Observers;

use App\Models\Beneficiary;
use App\Models\UnifiedUser;
use Illuminate\Support\Facades\Log;

class BeneficiaryObserver
{
    public function created(Beneficiary $beneficiary)
    {
        // Prevent duplicate UnifiedUser for this beneficiary
        $existing = UnifiedUser::where('beneficiary_id', $beneficiary->beneficiary_id)->first();
        $data = [
            'email' => null, // Beneficiaries do not use email for login
            'password' => $beneficiary->password,
            'first_name' => $beneficiary->first_name,
            'last_name' => $beneficiary->last_name,
            'mobile' => $beneficiary->mobile,
            'role_id' => 5, // or whatever role_id you use for beneficiaries
            'status' => $beneficiary->beneficiary_status_id,
            'user_type' => 'beneficiary',
            'cose_user_id' => null,
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'family_member_id' => null,
            'username' => $beneficiary->username,
        ];

        try {
            if ($existing) {
                $existing->update($data);
            } else {
                UnifiedUser::create($data);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to sync UnifiedUser for Beneficiary (create): ' . $e->getMessage());
        }
    }

    public function updated(Beneficiary $beneficiary)
    {
        $unifiedUser = UnifiedUser::where('beneficiary_id', $beneficiary->beneficiary_id)->first();
        $data = [
            'first_name' => $beneficiary->first_name,
            'last_name' => $beneficiary->last_name,
            'mobile' => $beneficiary->mobile,
            'status' => $beneficiary->beneficiary_status_id,
            'username' => $beneficiary->username,
            'password' => $beneficiary->password,
            'email' => null,
            'cose_user_id' => null,
            'family_member_id' => null,
        ];

        try {
            if ($unifiedUser) {
                $unifiedUser->update($data);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to sync UnifiedUser for Beneficiary (update): ' . $e->getMessage());
        }
    }

    public function deleted(Beneficiary $beneficiary)
    {
        try {
            UnifiedUser::where('beneficiary_id', $beneficiary->beneficiary_id)->delete();
        } catch (\Throwable $e) {
            Log::error('Failed to delete UnifiedUser for Beneficiary (delete): ' . $e->getMessage());
        }
    }
}