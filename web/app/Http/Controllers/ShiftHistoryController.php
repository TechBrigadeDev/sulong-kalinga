<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShiftHistoryController extends Controller
{
    /**
     * Display a listing of the current shift histories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // This will eventually load care worker shift data
        // For now, we're just returning the view with empty data
        
        return view('admin.shiftHistories', [
            'search' => $request->search,
            'date' => $request->date
        ]);
    }
    
    /**
     * Display a listing of archived shift histories.
     *
     * @return \Illuminate\Http\Response
     */
    public function archived(Request $request)
    {
        // This will eventually load archived shift history data
        // For now, we're just returning the view with empty data
        
        return view('admin.archivedShiftHistories', [
            'search' => $request->search,
            'date' => $request->date
        ]);
    }
}