<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class FamilyPortalMedicationScheduleController extends Controller
{
    public function index()
    {
        return view('familyPortal.medicationSchedule');
    }
}
?>
