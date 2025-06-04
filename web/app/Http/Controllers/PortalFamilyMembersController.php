<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FamilyMember;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;

class PortalFamilyMembersController extends Controller
{
    /**
     * Display the family members for the authenticated beneficiary
     */
    public function index()
    {
        // Get the authenticated beneficiary
        $beneficiary = Auth::guard('beneficiary')->user();
        
        // Get all family members related to this beneficiary
        $familyMembers = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)
            ->orderBy('is_primary_caregiver', 'desc')  // Primary caregivers first
            ->get();
        
        return view('beneficiaryPortal.familyMembers', [
            'beneficiary' => $beneficiary,
            'familyMembers' => $familyMembers
        ]);
    }
}