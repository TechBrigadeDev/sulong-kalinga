<?php

namespace App\Observers;

use App\Models\Beneficiary;
use App\Models\UnifiedUser;

class BeneficiaryObserver
{
    // public function created(Beneficiary $beneficiary)
    // {
    //     UnifiedUser::create([
    //         'email' => null, // Beneficiaries do not use email for login
    //         'password' => $beneficiary->password,
    //         'first_name' => $beneficiary->first_name,
    //         'last_name' => $beneficiary->last_name,
    //         'mobile' => $beneficiary->mobile,
    //         'role_id' => 5, // or whatever role_id you use for beneficiaries
    //         'status' => $beneficiary->beneficiary_status_id,
    //         'user_type' => 'beneficiary',
    //         'cose_user_id' => null,
    //         //'portal_account_id' => null,
    //         'username' => $beneficiary->username,
    //         'beneficiary_id' => $beneficiary->beneficiary_id,
    //     ]);
    // }

    // public function updated(Beneficiary $beneficiary)
    // {
    //     $unifiedUser = UnifiedUser::where('beneficiary_id', $beneficiary->beneficiary_id)->first();
    //     if ($unifiedUser) {
    //         $unifiedUser->update([
    //             'first_name' => $beneficiary->first_name,
    //             'last_name' => $beneficiary->last_name,
    //             'mobile' => $beneficiary->mobile,
    //             'status' => $beneficiary->beneficiary_status_id,
    //             'username' => $beneficiary->username,
    //             'password' => $beneficiary->password,
    //         ]);
    //     }
    // }

    // public function deleted(Beneficiary $beneficiary)
    // {
    //     UnifiedUser::where('beneficiary_id', $beneficiary->beneficiary_id)->delete();
    // }

    // // Remove all password hashing and username generation from observer
}