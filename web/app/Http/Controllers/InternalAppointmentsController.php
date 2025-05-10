<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class InternalAppointmentsController extends Controller
{
    public function index()
    {
        return view('admin.adminInternalAppointments');
    }
}
?>
