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
        if (auth()->user()->isCareManager()) {
            return view('careManager.careManagerAiSummary');
        }
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
        set_time_limit(600);

        $request->validate([
            'text' => 'required|string',
            'type' => 'required|in:assessment,evaluation',
            'max_sentences' => 'sometimes|integer|min:1|max:10',
            'care_plan_id' => 'required|integer',  // Add this to validate care plan ID
        ]);

        try {
            // Fix the URL by ensuring we're targeting the /summarize endpoint
            // Latest fix in calamancy api url is using nlp_host variable link after latest successful deploy @ June 27 2025 7:13AM
            $baseUrl = env('CALAMANCY_API_URL', 'http://calamancy-api:5000');
            $apiUrl = rtrim($baseUrl, '/') . '/summarize'; 
            \Log::info("Calling CalamanCy API at: " . $apiUrl);
            \Log::info("Request text length: " . strlen($request->text));
            
            $response = Http::timeout(60)
                ->withOptions([
                    'verify' => false, // Disabled assuming we won't apply SSL to internal services
                ])
                ->post($apiUrl, [
                    'text' => $request->text,
                    'type' => $request->type,
                    'max_sentences' => $request->max_sentences ?? 5
                ]);

            \Log::info("API Response Status: " . $response->status());
            
            if ($response->successful()) {
                $data = $response->json();
                \Log::info("API Response successful");
                
                // Save data to database immediately
                $carePlan = WeeklyCarePlan::findOrFail($request->care_plan_id);
                
                if ($request->type === 'assessment') {
                    $carePlan->assessment_summary_draft = $data['summary'] ?? 'No summary generated';
                    $carePlan->assessment_summary_sections = $data['sections'] ?? [];
                } else {
                    $carePlan->evaluation_summary_draft = $data['summary'] ?? 'No summary generated';
                    $carePlan->evaluation_summary_sections = $data['sections'] ?? [];
                }
                
                $carePlan->has_ai_summary = true;
                $saved = $carePlan->save();
                
                \Log::info("Summary saved to database: " . ($saved ? "YES" : "NO") . " for care plan ID: " . $request->care_plan_id);
                
                return response()->json([
                    'summary' => $data['summary'] ?? 'No summary generated',
                    'sections' => $data['sections'] ?? [],
                    'entities' => $data['entities'] ?? [],
                    'key_concerns' => $data['key_concerns'] ?? [],
                    'saved' => $saved
                ]);
            }

            \Log::error("API Error Response: " . $response->status() . " - " . substr($response->body(), 0, 1000));
            return response()->json([
                'error' => 'Summarization failed: ' . $response->status(),
                'details' => $response->body()
            ], 500);
        } catch (\Exception $e) {
            \Log::error("API Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
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
            'assessment_translation_draft' => 'sometimes|nullable|string',
            'evaluation_translation_draft' => 'sometimes|nullable|string',
            'assessment_translation_sections' => 'sometimes|nullable|array',
            'evaluation_translation_sections' => 'sometimes|nullable|array',
        ]);

        \Log::info("Updating summary for care plan ID: {$id}");
        \Log::info("Request data: " . json_encode($request->only([
            'assessment_summary_draft',
            'evaluation_summary_draft',
            'assessment_summary_sections',
            'evaluation_summary_sections'
        ])));

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

        if ($request->filled('assessment_translation_draft')) {
            $carePlan->assessment_translation_draft = $request->assessment_translation_draft;
        }
        if ($request->filled('evaluation_translation_draft')) {
            $carePlan->evaluation_translation_draft = $request->evaluation_translation_draft;
        }
        if ($request->has('assessment_translation_sections')) {
            $carePlan->assessment_translation_sections = $request->assessment_translation_sections;
        }
        if ($request->has('evaluation_translation_sections')) {
            $carePlan->evaluation_translation_sections = $request->evaluation_translation_sections;
        }
        
        $carePlan->has_ai_summary = true;
        $saved = $carePlan->save();
        
        \Log::info("Care plan update result: " . ($saved ? "SUCCESS" : "FAILED"));
        \Log::info("Updated data: " . json_encode($carePlan->only([
            'assessment_summary_draft',
            'evaluation_summary_draft',
            'assessment_summary_sections',
            'evaluation_summary_sections',
            'has_ai_summary'
        ])));
        
        return response()->json([
            'message' => 'Summary updated successfully',
            'carePlan' => $carePlan,
            'saved' => $saved
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
        set_time_limit(600);

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
                    'verify' => false, // Disabled assuming we won't apply SSL to internal services
                    // Can be changed to 'verify' => env('APP_ENV') === 'production' or 'verify' => env('API_VERIFY_SSL', true), if needed
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
        set_time_limit(600);

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
                        'verify' => false, // Still disabled even in production for internal services
                        // Can be changed to 'verify' => env('APP_ENV') === 'production' or 'verify' => env('API_VERIFY_SSL', true), if needed
                    ])
                    ->post($libreTranslateUrl . '/translate', [
                        'q' => $text,
                        'source' => 'tl', // Tagalog
                        'target' => 'en', // English
                        'format' => 'text',
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Apply post-processing to improve translation quality
                    $translatedText = $data['translatedText'];
                    $translatedText = $this->postProcessTranslation($translatedText);
                    
                    $translatedSections[$key] = $translatedText;
                }  else {
                    // If translation fails, keep the original
                    $translatedSections[$key] = $text;
                    \Log::error("Translation error for section {$key}: " . $response->status());
                }
            }

            // Save to database
            $carePlan = WeeklyCarePlan::findOrFail($request->weekly_care_plan_id);
            
            // Get gender from beneficiary if available, else fallback to detection
            $beneficiaryGender = strtolower($carePlan->beneficiary->gender ?? '');
            if ($beneficiaryGender === 'female') {
                $gender = 'female';
            } elseif ($beneficiaryGender === 'male') {
                $gender = 'male';
            } else {
                $gender = $this->detectMainSubjectGender($request->sections);
            }

            // Harmonize pronouns in each translated section
            foreach ($translatedSections as $key => $translatedText) {
                $translatedSections[$key] = $this->harmonizePronouns($translatedText, $gender);
            }
            // Harmonize executive summary if present
            if (isset($translatedSections['full_summary'])) {
                $translatedSections['full_summary'] = $this->harmonizePronouns($translatedSections['full_summary'], $gender);
            }
            
            if ($request->type === 'assessment') {
                $carePlan->assessment_translation_sections = $translatedSections;
                // Save only the executive summary translation as the draft
                $carePlan->assessment_translation_draft = $translatedSections['full_summary'] ?? '';
            } else {
                $carePlan->evaluation_translation_sections = $translatedSections;
                $carePlan->evaluation_translation_draft = $translatedSections['full_summary'] ?? '';
            }
            
            $carePlan->save();

            return response()->json([
                'translatedSections' => $translatedSections,
                'translatedText' => $translatedSections['full_summary'] ?? ''
            ]);
        } catch (\Exception $e) {
            \Log::error("Translation error: " . $e->getMessage());
            return response()->json([
                'error' => 'Translation service unavailable',
                'details' => $e->getMessage()
            ], 503);
        }
    }

    /**
     * Improve the quality of Filipino-to-English translations
     * 
     * @param string $text The translated text to process
     * @return string The improved translated text
     */
    private function postProcessTranslation($text)
    {
        if (!$text) {
            return $text;
        }
        
        // Fix common translation issues
        $fixes = [
            'the the' => 'the',
            'The the' => 'The',
            'a the' => 'the',
            'to the the' => 'to the',
            'in the the' => 'in the',
            'of the the' => 'of the',
            // Filipino specific term mappings
            'Nanay' => 'Mother',
            'Tatay' => 'Father',
            'Lola' => 'Grandmother',
            'Lolo' => 'Grandfather',
            'barangay' => 'village',
            ' po ' => ' ',  // Respectful marker not needed in English
            ' naman ' => ' ',  // Filler word not needed in English
            ' ba ' => ' ',  // Question marker not needed in English
        ];
        
        // Apply all fixes
        foreach ($fixes as $wrong => $correct) {
            $text = preg_replace('/\b' . preg_quote($wrong, '/') . '\b/', $correct, $text);
        }
        
        // Fix missing articles (common in Filipino->English translations)
        $articles = [
            '/\bis ([aeiou])/i' => 'is an $1',  // "is apple" -> "is an apple"
            '/\bis ([bcdfghjklmnpqrstvwxyz])/i' => 'is a $1',  // "is book" -> "is a book"
            '/\bhas ([aeiou])/i' => 'has an $1',  // "has orange" -> "has an orange"
            '/\bhas ([bcdfghjklmnpqrstvwxyz])/i' => 'has a $1'  // "has pen" -> "has a pen"
        ];
        
        // Apply article fixes
        foreach ($articles as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }
        
        // Fix spacing issues
        $text = preg_replace('/\s+/', ' ', $text);
        $text = preg_replace('/\s([,.;:])/', '$1', $text);
        
        // Ensure proper capitalization at the start
        $text = ucfirst(trim($text));
        
        // Fix capitalization after periods
        $text = preg_replace('/(\.\s+)([a-z])/', '$1' . strtoupper('$2'), $text);
        
        // Ensure ending with period
        if (!preg_match('/[.!?]$/', $text)) {
            $text .= '.';
        }
        
        // Medical terminology fixes
        $medicalTerms = [
            'high blood' => 'hypertension',
            'low blood' => 'hypotension',
        ];
        
        foreach ($medicalTerms as $incorrect => $correct) {
            $text = preg_replace('/\b' . preg_quote($incorrect, '/') . '\b/i', $correct, $text);
        }
        
        return $text;
    }

    private function detectMainSubjectGender($sections)
    {
        $text = implode(' ', $sections);
        $text = strtolower($text);

        // Expanded lists
        $female_terms = [
            'lola', 'nanay', 'ginang', 'ate', 'mrs.', 'ms.', 'ina', 'babae', 'mama', 'mom', 'mother', 'daughter', 'sister', 'tita', 'aunt', 'apo', 'miss', 'madam', 'ma\'am', 'wife', 'asawa', 'girlfriend', 'fiancée', 'lola', 'lola', 'lola'
        ];
        $male_terms = [
            'lolo', 'tatay', 'ginoong', 'kuya', 'mr.', 'ama', 'lalaki', 'papa', 'dad', 'father', 'son', 'brother', 'tito', 'uncle', 'apo', 'sir', 'husband', 'asawa', 'boyfriend', 'fiancé'
        ];

        foreach ($female_terms as $term) {
            if (strpos($text, $term) !== false) {
                return 'female';
            }
        }
        foreach ($male_terms as $term) {
            if (strpos($text, $term) !== false) {
                return 'male';
            }
        }

        // Fallback: check for pronouns in the text
        if (preg_match('/\b(she|her|hers)\b/', $text)) {
            return 'female';
        }
        if (preg_match('/\b(he|him|his)\b/', $text)) {
            return 'male';
        }

        // Default to neutral if not found
        return 'neutral';
    }

    private function harmonizePronouns($text, $gender)
    {
        if ($gender === 'female') {
            $patterns = [
                // Subject/Object/Reflexive
                '/\bHe\b/' => 'She',
                '/\bhe\b/' => 'she',
                '/\bHis\b/' => 'Her',
                '/\bhis\b/' => 'her',
                '/\bHim\b/' => 'Her',
                '/\bhim\b/' => 'her',
                '/\bHimself\b/' => 'Herself',
                '/\bhimself\b/' => 'herself',
                '/\bFather\b/' => 'Mother',
                '/\bDad\b/' => 'Mom',
                '/\bgrandfather\b/' => 'grandmother',
                '/\bson\b/' => 'daughter',
                '/\buncle\b/' => 'aunt',
                '/\bMr\./' => 'Ms.',
                '/\bSir\b/' => 'Ma\'am',
                // Possessive
                '/\bhis\'s\b/' => 'her',
                '/\bhis\b/' => 'her',
                '/\bhers\b/' => 'hers',
            ];
        } elseif ($gender === 'male') {
            $patterns = [
                '/\bShe\b/' => 'He',
                '/\bshe\b/' => 'he',
                '/\bHer\b/' => 'His',
                '/\bher\b/' => 'his',
                '/\bHerself\b/' => 'Himself',
                '/\bherself\b/' => 'himself',
                '/\bMother\b/' => 'Father',
                '/\bMom\b/' => 'Dad',
                '/\bgrandmother\b/' => 'grandfather',
                '/\bdaughter\b/' => 'son',
                '/\baunt\b/' => 'uncle',
                '/\bMs\./' => 'Mr.',
                '/\bMa\'am\b/' => 'Sir',
                // Possessive
                '/\bher\'s\b/' => 'his',
                '/\bhers\b/' => 'his',
            ];
        } else { // Neutral/Other: use they/them/their
            $patterns = [
                // Subject/Object/Reflexive
                '/\bHe\b/' => 'They',
                '/\bhe\b/' => 'they',
                '/\bShe\b/' => 'They',
                '/\bshe\b/' => 'they',
                '/\bHis\b/' => 'Their',
                '/\bhis\b/' => 'their',
                '/\bHer\b/' => 'Their',
                '/\bher\b/' => 'their',
                '/\bHim\b/' => 'Them',
                '/\bhim\b/' => 'them',
                '/\bHerself\b/' => 'Themself',
                '/\bherself\b/' => 'themself',
                '/\bHimself\b/' => 'Themself',
                '/\bhimself\b/' => 'themself',
                // '/\bFather\b/' => 'Parent',
                // '/\bMother\b/' => 'Parent',
                // '/\bDad\b/' => 'Parent',
                // '/\bMom\b/' => 'Parent',
                // '/\bgrandfather\b/' => 'grandparent',
                // '/\bgrandmother\b/' => 'grandparent',
                // '/\bson\b/' => 'child',
                // '/\bdaughter\b/' => 'child',
                // '/\buncle\b/' => 'relative',
                // '/\baunt\b/' => 'relative',
                // '/\bMr\./' => 'Mx.',
                // '/\bMs\./' => 'Mx.',
                // '/\bSir\b/' => 'Mx.',
                // '/\bMa\'am\b/' => 'Mx.',
                // Possessive
                '/\bher\'s\b/' => 'theirs',
                '/\bhers\b/' => 'theirs',
                '/\bhis\'s\b/' => 'theirs',
                '/\bhis\b/' => 'their',
            ];
        }

        // Apply all replacements
        foreach ($patterns as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }
        return $text;
    }

}

