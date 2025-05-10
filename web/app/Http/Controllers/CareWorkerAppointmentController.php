<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class CareWorkerAppointmentController extends Controller
{
    public function index()
    {
        return view('admin.adminCareWorkerAppointments');
    }
}
?>
