<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class FamilyPortalVisitationScheduleController extends Controller
{
    public function index()
    {
        return view('familyPortal.visitationSchedule');
    }
}
?>
