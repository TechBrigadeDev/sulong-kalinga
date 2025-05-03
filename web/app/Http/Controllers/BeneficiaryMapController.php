<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class BeneficiaryMapController extends Controller
{
    public function index()
    {
        return view('admin.beneficiaryMap');
    }
}
?>
