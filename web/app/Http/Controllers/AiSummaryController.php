<?php

namespace App\Http\Controllers;

use App\Models\WeeklyCarePlan;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiSummaryController extends Controller
{
    public function index()
    {
        return view('admin.adminAiSummary');
    }

    public function search(Request $request)
    {
        $query = WeeklyCarePlan::query()
        ->select(
            'weekly_care_plans.*', 
            'beneficiaries.first_name', 
            'beneficiaries.last_name',
            'users.first_name as care_worker_first_name',
            'users.last_name as care_worker_last_name'
        )
        ->join('beneficiaries', 'weekly_care_plans.beneficiary_id', '=', 'beneficiaries.beneficiary_id')
        ->leftJoin('cose_users as users', 'weekly_care_plans.care_worker_id', '=', 'users.id');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('beneficiaries.first_name', 'LIKE', "%{$search}%")
                  ->orWhere('beneficiaries.last_name', 'LIKE', "%{$search}%")
                  ->orWhere('weekly_care_plans.weekly_care_plan_id', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('weekly_care_plans.created_at', [$request->date_from, $request->date_to]);
        }

        $carePlans = $query->orderBy('weekly_care_plans.created_at', 'desc')
                    ->paginate(10);

        return response()->json($carePlans);
    }

    public function getCarePlan($id)
    {
        $carePlan = WeeklyCarePlan::with(['beneficiary', 'careWorker', 'author'])
                  ->findOrFail($id);
                  
        return response()->json($carePlan);
    }

    public function summarize(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'type' => 'required|in:assessment,evaluation',
            'max_sentences' => 'sometimes|integer|min:1|max:10',
        ]);

        try {
            $apiUrl = env('CALAMANCY_API_URL', 'http://calamancy-api:5000/summarize');
            
            $response = Http::post($apiUrl, [
                'text' => $request->text,
                'max_sentences' => $request->max_sentences ?? 3,
                'sectioned' => true,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Summarization failed',
                'details' => $response->body()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Summarization service unavailable',
                'details' => $e->getMessage()
            ], 503);
        }
    }
    
    public function updateSummary(Request $request, $id)
    {
        $request->validate([
            'assessment_summary_draft' => 'sometimes|nullable|string',
            'evaluation_summary_draft' => 'sometimes|nullable|string',
            'assessment_summary_sections' => 'sometimes|nullable|array',
            'evaluation_summary_sections' => 'sometimes|nullable|array',
        ]);

        $carePlan = WeeklyCarePlan::findOrFail($id);
        
        if ($request->filled('assessment_summary_draft')) {
            $carePlan->assessment_summary_draft = $request->assessment_summary_draft;
        }
        
        if ($request->filled('evaluation_summary_draft')) {
            $carePlan->evaluation_summary_draft = $request->evaluation_summary_draft;
        }
        
        if ($request->has('assessment_summary_sections')) {
            $carePlan->assessment_summary_sections = $request->assessment_summary_sections;
        }
        
        if ($request->has('evaluation_summary_sections')) {
            $carePlan->evaluation_summary_sections = $request->evaluation_summary_sections;
        }
        
        $carePlan->has_ai_summary = true;
        $carePlan->save();
        
        return response()->json([
            'message' => 'Summary updated successfully',
            'carePlan' => $carePlan
        ]);
    }
    
    public function finalizeSummary(Request $request, $id)
    {
        $request->validate([
            'assessment_summary_final' => 'sometimes|nullable|string',
            'evaluation_summary_final' => 'sometimes|nullable|string',
        ]);

        $carePlan = WeeklyCarePlan::findOrFail($id);
        
        if ($request->filled('assessment_summary_final')) {
            $carePlan->assessment_summary_final = $request->assessment_summary_final;
        }
        
        if ($request->filled('evaluation_summary_final')) {
            $carePlan->evaluation_summary_final = $request->evaluation_summary_final;
        }
        
        $carePlan->save();
        
        return response()->json([
            'message' => 'Summary finalized successfully',
            'carePlan' => $carePlan
        ]);
    }
}