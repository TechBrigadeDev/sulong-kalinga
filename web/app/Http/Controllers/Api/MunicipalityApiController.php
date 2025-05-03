<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Municipality;
use Illuminate\Http\Request;

class MunicipalityApiController extends Controller
{
    /**
     * Display a listing of municipalities.
     */
    public function index(Request $request)
    {
        $query = Municipality::query();
        
        // Add search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('municipality_name', 'LIKE', "%{$search}%");
        }
        
        // Add filtering by province
        if ($request->has('province')) {
            $query->where('province', $request->get('province'));
        }
        
        $municipalities = $query->orderBy('municipality_name')->get();
        
        return response()->json([
            'success' => true,
            'municipalities' => $municipalities
        ]);
    }

    /**
     * Display the specified municipality.
     */
    public function show($id)
    {
        $municipality = Municipality::findOrFail($id);
        
        // Get statistics about users in this municipality
        $stats = [
            'beneficiary_count' => \App\Models\User::where('role_id', 5)
                ->where('assigned_municipality_id', $id)
                ->count(),
            'careworker_count' => \App\Models\User::where('role_id', 3)
                ->where('assigned_municipality_id', $id)
                ->count(),
            'caremanager_count' => \App\Models\User::where('role_id', 2)
                ->where('assigned_municipality_id', $id)
                ->count(),
        ];
        
        return response()->json([
            'success' => true,
            'municipality' => $municipality,
            'stats' => $stats
        ]);
    }

    /**
     * Get unique provinces for filtering
     */
    public function provinces()
    {
        $provinces = Municipality::select('province')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');
            
        return response()->json([
            'success' => true,
            'provinces' => $provinces
        ]);
    }
}