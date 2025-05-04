<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BeneficiaryApiController extends Controller
{
    /**
     * Display a listing of beneficiaries.
     */
    public function index(Request $request)
    {
        $query = Beneficiary::query();
            
        // Add search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Add filtering by municipality
        if ($request->has('municipality_id')) {
            $query->where('assigned_municipality_id', $request->get('municipality_id'));
        }
        
        // Add filtering by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }
        
        $beneficiaries = $query->orderBy('first_name')->get();
        
        return response()->json([
            'success' => true,
            'beneficiaries' => $beneficiaries
        ]);
    }

    /**
     * Display the specified beneficiary.
     */
    public function show($id)
    {
        $beneficiary = Beneficiary::findOrFail($id);
            
        return response()->json([
            'success' => true,
            'beneficiary' => $beneficiary
        ]);
    }

    /**
     * Store a newly created beneficiary.
     */
    public function store(Request $request)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required', 'email',
                Rule::unique('beneficiaries', 'email'),
            ],
            'mobile' => [
                'required', 'string',
                Rule::unique('beneficiaries', 'mobile'),
            ],
            // Add other required fields as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $beneficiary = Beneficiary::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $beneficiary
        ]);
    }

    /**
     * Update the specified beneficiary.
     */
    public function update(Request $request, $id)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $beneficiary = Beneficiary::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes', 'required', 'email',
                Rule::unique('beneficiaries', 'email')->ignore($beneficiary->id),
            ],
            'mobile' => [
                'sometimes', 'required', 'string',
                Rule::unique('beneficiaries', 'mobile')->ignore($beneficiary->id),
            ],
            // Add other fields as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $beneficiary->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $beneficiary
        ]);
    }

    /**
     * Change beneficiary status (admin only)
     */
    public function changeStatus(Request $request, $id)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $beneficiary = Beneficiary::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'status' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $beneficiary->status = $request->status;
        $beneficiary->save();

        return response()->json([
            'success' => true,
            'data' => $beneficiary
        ]);
    }

    /**
     * Remove the specified beneficiary.
     */
    public function destroy(Request $request, $id)
    {
        if (!in_array($request->user()->role_id, [1, 2, 3])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $beneficiary = Beneficiary::findOrFail($id);
        $beneficiary->delete();

        return response()->json(['success' => true]);
    }
}