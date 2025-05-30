<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class FamilyPortalCarePlanController extends Controller
{
    public function index()
    {
        return view('familyPortal.carePlan');
    }

    public function allCarePlans()
    {
        // Logic to retrieve all care plans
        $carePlans = []; // Replace with actual data retrieval logic

        return view('familyPortal.viewAllCarePlan', ['carePlans' => $carePlans]);
    }
}
?>
