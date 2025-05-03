<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CareWorkerReportsController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->input('search');
        $filterType = $request->input('filter');
        $sortOrder = $request->input('sort', 'desc');

        // Start building the query
        $query = DB::table('weekly_care_plans as wcp')
            ->leftJoin('users as u', 'wcp.created_by', '=', 'u.id')
            ->leftJoin('beneficiaries as b', 'wcp.beneficiary_id', '=', 'b.beneficiary_id')
            ->where('wcp.created_by', $userId) // Only show reports authored by this care worker
            ->select(
                'wcp.weekly_care_plan_id as report_id',
                DB::raw("'Weekly Care Plan' as report_type"),
                'u.first_name as author_first_name',
                'u.last_name as author_last_name',
                'b.first_name as beneficiary_first_name',
                'b.last_name as beneficiary_last_name',
                'wcp.created_at'
            );

        // Apply search if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('u.first_name', 'LIKE', "%{$search}%")
                  ->orWhere('u.last_name', 'LIKE', "%{$search}%")
                  ->orWhere('b.first_name', 'LIKE', "%{$search}%")
                  ->orWhere('b.last_name', 'LIKE', "%{$search}%");
            });
        }

        // Apply sorting
        $query->orderBy('wcp.created_at', $sortOrder);

        $reports = $query->paginate(10);

        return view('careWorker.reportsManagement', compact('reports', 'search', 'filterType', 'sortOrder'));
    }
}