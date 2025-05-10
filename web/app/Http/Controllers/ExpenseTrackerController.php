<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class ExpenseTrackerController extends Controller
{
    public function index()
    {
        return view('admin.adminExpenseTracker');
    }
}
?>
