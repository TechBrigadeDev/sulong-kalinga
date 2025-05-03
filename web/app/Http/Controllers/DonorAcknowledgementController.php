<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class DonorAcknowledgementController extends Controller
{
    public function index()
    {
        return view('admin.donorAcknowledgement');
    }
}
?>
