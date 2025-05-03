<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class HealthMonitoringController extends Controller
{
    public function index()
    {
        return view('admin.adminHealthMonitoring');
    }
}
?>
