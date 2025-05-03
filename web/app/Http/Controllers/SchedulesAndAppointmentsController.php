<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class SchedulesAndAppointmentsController extends Controller
{
    public function index()
    {
        return view('admin.schedulesAndAppointments');
    }
}
?>
