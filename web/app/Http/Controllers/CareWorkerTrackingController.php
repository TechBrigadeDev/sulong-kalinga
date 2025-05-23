<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class CareWorkerTrackingController extends Controller
{
    public function index()
    {
        return view('admin.adminCareWorkerTracking');
    }
}
?>
