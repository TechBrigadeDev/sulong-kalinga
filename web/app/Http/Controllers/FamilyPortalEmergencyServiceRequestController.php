<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class FamilyPortalEmergencyServiceRequestController extends Controller
{
    public function index()
    {
        return view('familyPortal.emergencyAndService');
    }
}
?>
