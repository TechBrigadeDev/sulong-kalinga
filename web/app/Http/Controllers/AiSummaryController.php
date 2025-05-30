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
            // Fix the URL by ensuring we're targeting the /summarize endpoint
            $baseUrl = env('CALAMANCY_API_URL', 'http://calamancy-api:5000');
            $apiUrl = rtrim($baseUrl, '/') . '/summarize'; 
            \Log::info("Calling CalamanCy API at: " . $apiUrl);
            \Log::info("Request text length: " . strlen($request->text));
            
            $response = Http::timeout(60)
                ->withOptions([
                    'verify' => false, // Disable SSL verification for local development
                ])
                ->post($apiUrl, [
                    'text' => $request->text,
                    'type' => $request->type,  // Make sure this is passed
                    'max_sentences' => $request->max_sentences ?? 3
                ]);

            \Log::info("API Response Status: " . $response->status());
            
            if ($response->successful()) {
                $data = $response->json();
                \Log::info("API Response successful");
                
                return response()->json([
                    'summary' => $data['summary'] ?? '',
                    'sections' => $data['sections'] ?? [],
                    'entities' => $data['entities'] ?? [],
                    'key_concerns' => $data['key_concerns'] ?? []
                ]);
            }

            \Log::error("API Error Response: " . $response->status() . " - " . substr($response->body(), 0, 1000));
            return response()->json([
                'error' => 'Summarization failed: ' . $response->status(),
                'details' => $response->body()
            ], 500);
        } catch (\Exception $e) {
            \Log::error("API Exception: " . $e->getMessage());
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

    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'weekly_care_plan_id' => 'required|integer',
            'type' => 'required|in:assessment,evaluation'
        ]);

        try {
            $libreTranslateUrl = env('LIBRETRANSLATE_URL', 'http://libretranslate:5000');
            
            \Log::info("Calling LibreTranslate API at: " . $libreTranslateUrl);
            
            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false, // Disable SSL verification for local development
                ])
                ->post($libreTranslateUrl . '/translate', [
                    'q' => $request->text,
                    'source' => 'tl', // Tagalog
                    'target' => 'en', // English
                    'format' => 'text',
                ]);
            
            \Log::info("API Response Status: " . $response->status());
            
            if ($response->successful()) {
                $data = $response->json();
                \Log::info("Translation successful");
                
                // Update the database with the translation
                $carePlan = WeeklyCarePlan::findOrFail($request->weekly_care_plan_id);
                
                if ($request->type === 'assessment') {
                    $carePlan->assessment_translation_draft = $data['translatedText'];
                } else {
                    $carePlan->evaluation_translation_draft = $data['translatedText'];
                }
                
                $carePlan->save();
                
                return response()->json([
                        'translatedText' => $data['translatedText'] // Make sure this matches what your JS expects
                ]);
            }

            \Log::error("Translation Error: " . $response->status() . " - " . $response->body());
            return response()->json([
                'error' => 'Translation failed: ' . $response->status(),
                'details' => $response->body()
            ], 500);
        } catch (\Exception $e) {
            \Log::error("Translation Exception: " . $e->getMessage());
            return response()->json([
                'error' => 'Translation service unavailable',
                'details' => $e->getMessage()
            ], 503);
        }
    }

    public function translateSections(Request $request)
    {
        $request->validate([
            'sections' => 'required|array',
            'weekly_care_plan_id' => 'required|integer',
            'type' => 'required|in:assessment,evaluation'
        ]);

        $translatedSections = [];
        $libreTranslateUrl = env('LIBRETRANSLATE_URL', 'http://libretranslate:5000');
        \Log::info("Translating sections for care plan #{$request->weekly_care_plan_id}");

        try {
            foreach ($request->sections as $key => $text) {
                // Skip empty sections
                if (empty(trim($text))) {
                    $translatedSections[$key] = '';
                    continue;
                }

                \Log::info("Translating section: {$key}");
                
                $response = Http::timeout(30)
                    ->withOptions([
                        'verify' => false, // For local development
                    ])
                    ->post($libreTranslateUrl . '/translate', [
                        'q' => $text,
                        'source' => 'tl', // Tagalog
                        'target' => 'en', // English
                        'format' => 'text',
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $translatedSections[$key] = $data['translatedText'];
                } else {
                    // If translation fails, keep the original
                    $translatedSections[$key] = $text;
                    \Log::error("Translation error for section {$key}: " . $response->status());
                }
            }

            // Save to database
            $carePlan = WeeklyCarePlan::findOrFail($request->weekly_care_plan_id);
            
            if ($request->type === 'assessment') {
                $carePlan->assessment_translation_sections = $translatedSections;
                // Also save the combined text for compatibility
                $carePlan->assessment_translation_draft = implode("\n\n", array_values($translatedSections));
            } else {
                $carePlan->evaluation_translation_sections = $translatedSections;
                $carePlan->evaluation_translation_draft = implode("\n\n", array_values($translatedSections));
            }
            
            $carePlan->save();

            return response()->json([
                'translatedSections' => $translatedSections,
                'translatedText' => implode("\n\n", array_values($translatedSections))
            ]);
        } catch (\Exception $e) {
            \Log::error("Translation error: " . $e->getMessage());
            return response()->json([
                'error' => 'Translation service unavailable',
                'details' => $e->getMessage()
            ], 503);
        }
    }

}

