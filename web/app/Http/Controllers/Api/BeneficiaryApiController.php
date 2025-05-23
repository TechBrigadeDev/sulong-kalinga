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
                $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
                //  ->orWhere('email', 'LIKE', "%{$search}%");
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
            // File path fields
            'photo' => 'nullable|string|max:255',
            'care_service_agreement_doc' => 'nullable|string|max:255',
            'general_care_plan_doc' => 'nullable|string|max:255',
            'beneficiary_signature' => 'nullable|string|max:255',
            'care_worker_signature' => 'nullable|string|max:255',
            // Add other required fields as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $beneficiary = Beneficiary::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
            // File path fields
            'photo' => $request->input('photo'),
            'care_service_agreement_doc' => $request->input('care_service_agreement_doc'),
            'general_care_plan_doc' => $request->input('general_care_plan_doc'),
            'beneficiary_signature' => $request->input('beneficiary_signature'),
            'care_worker_signature' => $request->input('care_worker_signature'),
            // Add other fields as needed
        ]);

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
            // File path fields
            'photo' => 'nullable|string|max:255',
            'care_service_agreement_doc' => 'nullable|string|max:255',
            'general_care_plan_doc' => 'nullable|string|max:255',
            'beneficiary_signature' => 'nullable|string|max:255',
            'care_worker_signature' => 'nullable|string|max:255',
            // Add other fields as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $beneficiary->fill($request->only([
            'first_name',
            'last_name',
            'email',
            'mobile',
            // File path fields
            'photo',
            'care_service_agreement_doc',
            'general_care_plan_doc',
            'beneficiary_signature',
            'care_worker_signature',
            // Add other fields as needed
        ]));
        $beneficiary->save();

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