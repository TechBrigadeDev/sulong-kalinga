<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class CareWorkerPerformanceController extends Controller
{
    public function index()
    {
        return view('admin.careWorkerPerformance');
    }
}
?>
