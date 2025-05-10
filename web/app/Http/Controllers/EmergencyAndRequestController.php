<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class EmergencyAndRequestController extends Controller
{
    public function index()
    {
        return view('admin.adminEmergencyRequest');
    }

    public function viewHistory()
    {
        return view('admin.adminEmergencyRequestHistory');
    }
}
?>
