<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnifiedUser;
use Illuminate\Http\Request;

class AdminApiController extends Controller
{
    // List all admins
    public function index(Request $request)
    {
        // Only allow Admin to view admins
        if ($request->user()->role_id !== 1) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $admins = UnifiedUser::where('role_id', 1)->get();
        return response()->json(['success' => true, 'admins' => $admins]);
    }

    // Show a single admin
    public function show(Request $request, $id)
    {
        // Only allow Admin to view admins
        if ($request->user()->role_id !== 1) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $admin = UnifiedUser::where('role_id', 1)->findOrFail($id);
        return response()->json(['success' => true, 'admin' => $admin]);
    }
}
