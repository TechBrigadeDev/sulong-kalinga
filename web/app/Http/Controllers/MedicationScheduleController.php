<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class MedicationScheduleController extends Controller
{
    public function index()
    {
        return view('admin.adminMedicationSchedule');
    }
}
?>
