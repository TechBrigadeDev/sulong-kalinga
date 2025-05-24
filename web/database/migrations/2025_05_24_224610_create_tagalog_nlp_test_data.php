<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations to add Tagalog test data for NLP testing.
     */
    public function up(): void
    {
        // Get care worker ID first to use consistently across all records
        $careWorkerId = DB::table('cose_users')
            ->where('role_id', 3)
            ->value('id');
            
        if (!$careWorkerId) {
            // Fallback to admin if no care workers exist
            $careWorkerId = DB::table('cose_users')
                ->where('role_id', 1)
                ->value('id') ?? 1;
        }
        
        // Create vital signs records first
        $vitalSignsIds = $this->createVitalSigns($careWorkerId);

        // Insert weekly care plans with Tagalog assessments and evaluations
        $this->insertWeeklyCarePlans($vitalSignsIds, $careWorkerId);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Find the IDs we inserted to safely remove only our test data
        $weeklyCarePlanIds = DB::table('weekly_care_plans')
            ->whereIn('assessment', [
                'Mabigat na ang katawan ni nanay at pabagsak na to kung umupo. Mahinay lang din kung maglakad gamit ang kanyang assistive device. Medyo Malabo na ang mata ni nanay at malalim na rin ang pandinig niya. Nagpapasalamat si nanay dahil hindi pa siya iniiwan ng kanyang pamangkin na dating inalagaan niya. Hanggat hindi pa napapaayos ang kanyang Bahay ay hindi pa daw ito aalis. Sinisiguro nito na hindi mababasa si nanay pag mag tag-ulan. Hindi na minsan kaya ni nanay maggupit ng kanyang kuko dahil Malabo na ang kanyang mata.',
                'Malakas pa si nanay pero minsan ang paglakad at paggalaw-galaw niya ay kilos ng parang lasing dahil nangangatal-ngatal ito Lalo na ang kanyang ulo. Nakwento ni nanay na antagal pa daw mag-release ng social pension, wala na daw silang pambili ng bigas at iba pang kailangan araw-araw. Ubos na ang pera na natanggap la nung eleksyon. Lagi pa namang nahingi ang kanyang asawa ng tinapay at gatas. Mahahaba na uli ang mga kuko ni nanay. Daing lagi ni nanay ang kanyang masakit na balikat.',
                'Mahina na si nanay kaya hirap ito sa kanyang pag-upo at paglalakad Lalo na kung hindi gamit ang kanyang assistive device. Malabo na ang paningin ni nanay at malalim na ang kanyang pandinig. Naikwento ng tagapag-alaga ni nanay na lagi na lang natakas si nanay tulad ngayon umaga bago ako dumating, naalis na sana si nanay at kung hindi napansin ng anak ay baka kung saan na naman nakaabot tapos natumba na uli. Buti nga at nakapaglakad-lakad na uli si nanay hindi katulad nung nakaraang pagbisita ko, nakahiga na lang. Mahahaba na uli ang mga kuko ni nanay sa kamay at paa. Masakit ang balakang ni nanay gawa ng kanyang malimit na pagkatumba. Bago ng kain si nanay pagdating ko sa kanila kaya niyaya ko ito sa labas para makalanghap ng sariwang hangin at makapagpasikat sa araw. Nabawasan ng kaunti ang sakit na nararamdaman niya at nakapag-exercise na uli si nanay.',
                'Medyo hirap si tatay sa kanyang pag-upo at paglalakad Lalo na kung wala ang kanyang assistive device. Naikwento ni tatay na dati daw ay lagi siyang naduduwal at nagsusuka tuwing pagkatapos nyang kumain, ngayon ay hindi na Kahit marami siyang kainin, lagging nanghihinayang siya sa kanyang kinakain at inilalabas din ang pagkatapos. Isinama din daw siya ng kanyang apo sa lugar nila sa Lapinig. Matalas na at mahahaba na uli ang mga kuko ni tatay. Mainit sa loob ng Bahay nina tatay kaya niyaya ko ito sa labas na magpahangin at magpasikat ng araw.'
            ])
            ->pluck('weekly_care_plan_id');
            
        // Delete the test care plans and related data
        if (!empty($weeklyCarePlanIds)) {
            // Delete interventions first (foreign key constraints)
            DB::table('weekly_care_plan_interventions')
                ->whereIn('weekly_care_plan_id', $weeklyCarePlanIds)
                ->delete();
                
            // Get the vital signs ids before deleting care plans
            $vitalSignsIds = DB::table('weekly_care_plans')
                ->whereIn('weekly_care_plan_id', $weeklyCarePlanIds)
                ->pluck('vital_signs_id');
                
            // Delete weekly care plans
            DB::table('weekly_care_plans')
                ->whereIn('weekly_care_plan_id', $weeklyCarePlanIds)
                ->delete();
                
            // Delete vital signs
            if (!empty($vitalSignsIds)) {
                DB::table('vital_signs')
                    ->whereIn('vital_signs_id', $vitalSignsIds)
                    ->delete();
            }
        }
    }
    
    /**
     * Create vital signs records for the test data
     * 
     * @param int $careWorkerId - The care worker ID to use as created_by
     * @return array - List of created vital signs IDs
     */
    private function createVitalSigns($careWorkerId)
    {
        $vitalSignsData = [
            [
                'blood_pressure' => '130/80',
                'body_temperature' => 36.5,
                'pulse_rate' => 72,
                'respiratory_rate' => 18,
                'created_by' => $careWorkerId,  // Use care worker ID consistently
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'blood_pressure' => '140/90',
                'body_temperature' => 36.8,
                'pulse_rate' => 78,
                'respiratory_rate' => 20,
                'created_by' => $careWorkerId,  // Use care worker ID consistently
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'blood_pressure' => '125/85',
                'body_temperature' => 37.1,
                'pulse_rate' => 70,
                'respiratory_rate' => 19,
                'created_by' => $careWorkerId,  // Use care worker ID consistently
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'blood_pressure' => '135/75',
                'body_temperature' => 36.7,
                'pulse_rate' => 75,
                'respiratory_rate' => 17,
                'created_by' => $careWorkerId,  // Use care worker ID consistently
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        $vitalSignsIds = [];
        foreach ($vitalSignsData as $data) {
            // Specify the primary key column name as the second parameter
            $vitalSignsIds[] = DB::table('vital_signs')->insertGetId($data, 'vital_signs_id');
        }

        return $vitalSignsIds;
    }

    /**
     * Insert the weekly care plans with the Tagalog test data
     * 
     * @param array $vitalSignsIds - The vital signs IDs to link to care plans
     * @param int $careWorkerId - The care worker ID to use as created_by and care_worker_id
     */
    private function insertWeeklyCarePlans($vitalSignsIds, $careWorkerId)
    {
        // Get care manager ID (supervisor)
        $careManagerId = DB::table('cose_users')
            ->where('role_id', 2)
            ->value('id') ?? $careWorkerId;  // Fallback to care worker if no manager exists
            
        // Get beneficiary IDs - we need 4 beneficiaries
        $beneficiaryIds = DB::table('beneficiaries')
            ->limit(4)
            ->pluck('beneficiary_id')
            ->toArray();
            
        // If we don't have enough beneficiaries, use the same one multiple times
        while (count($beneficiaryIds) < 4) {
            $beneficiaryIds[] = $beneficiaryIds[0] ?? 1;
        }
        
        // Get available intervention categories and interventions for random assignment
        $careCategories = DB::table('care_categories')->pluck('care_category_id')->toArray();
        $interventions = DB::table('interventions')->pluck('intervention_id')->toArray();
        
        // Fallback in case no interventions or categories exist
        if (empty($careCategories)) {
            $careCategories = [1, 2, 3]; // Dummy IDs
        }
        
        if (empty($interventions)) {
            $interventions = [1, 2, 3, 4, 5]; // Dummy IDs
        }
        
        // Assessment and evaluation pairs as provided
        $assessmentEvaluationPairs = [
            [
                'assessment' => 'Mabigat na ang katawan ni nanay at pabagsak na to kung umupo. Mahinay lang din kung maglakad gamit ang kanyang assistive device. Medyo Malabo na ang mata ni nanay at malalim na rin ang pandinig niya. Nagpapasalamat si nanay dahil hindi pa siya iniiwan ng kanyang pamangkin na dating inalagaan niya. Hanggat hindi pa napapaayos ang kanyang Bahay ay hindi pa daw ito aalis. Sinisiguro nito na hindi mababasa si nanay pag mag tag-ulan. Hindi na minsan kaya ni nanay maggupit ng kanyang kuko dahil Malabo na ang kanyang mata.',
                'evaluation' => 'Kahit mahinay ang mga galawan ni nanay, pinipilit naman niyang makaupo at makalakad mag-isa pero mas maigi pa rin pag may naalalay o nasuporta dahil makakasiguro na safe si nanay. Kailangan kong ulit-ulitin ang sasabihin at itatanong kay nanay hanggang sa marinig niya ito at dapat medyo lakasan ko ang aking boses kapag municipal ap a kanya. Sinabi ko kay nanay na hindi lahat ng mga inalagaan mong ta pa hanggang sa lumaki ay tatanaw ng utang na loob minsan na lang ngayon panahon ang ganon. Kaya nagpapasalamat si nanay dahil hindi man siya nagkaanak, napalaki naman niya ng tama ang mga pamangking pinaalagaan sa kanya dati. Malinis na tignan ang mga kuko ni nanay. Naging maikli na ang mga ito.'
            ],
            [
                'assessment' => 'Malakas pa si nanay pero minsan ang paglakad at paggalaw-galaw niya ay kilos ng parang lasing dahil nangangatal-ngatal ito Lalo na ang kanyang ulo. Nakwento ni nanay na antagal pa daw mag-release ng social pension, wala na daw silang pambili ng bigas at iba pang kailangan araw-araw. Ubos na ang pera na natanggap la nung eleksyon. Lagi pa namang nahingi ang kanyang asawa ng tinapay at gatas. Mahahaba na uli ang mga kuko ni nanay. Daing lagi ni nanay ang kanyang masakit na balikat.',
                'evaluation' => 'Nasiguradong safe ang pag-upo at paglakad-lakad ni nanay. Ang sabi ko kay nanay, ang perang hindi pinagpaguran ay madaling nauubos. Parang hangin lang na dumadaan. Pinayuhan ko din si nanay na kung maaari ay wag masyadong umasa a kung anong ibibigay ng gobyerno. Gumawa ng ibang paraan pano malalampasan o makakaraos sa araw-araw na pangangailangan. Naging maikli na uli ang mga kuko ni nanay. Naibsan Kahit papano ang masakit niyang balikat.'
            ],
            [
                'assessment' => 'Mahina na si nanay kaya hirap ito sa kanyang pag-upo at paglalakad Lalo na kung hindi gamit ang kanyang assistive device. Malabo na ang paningin ni nanay at malalim na ang kanyang pandinig. Naikwento ng tagapag-alaga ni nanay na lagi na lang natakas si nanay tulad ngayon umaga bago ako dumating, naalis na sana si nanay at kung hindi napansin ng anak ay baka kung saan na naman nakaabot tapos natumba na uli. Buti nga at nakapaglakad-lakad na uli si nanay hindi katulad nung nakaraang pagbisita ko, nakahiga na lang. Mahahaba na uli ang mga kuko ni nanay sa kamay at paa. Masakit ang balakang ni nanay gawa ng kanyang malimit na pagkatumba. Bago ng kain si nanay pagdating ko sa kanila kaya niyaya ko ito sa labas para makalanghap ng sariwang hangin at makapagpasikat sa araw. Nabawasan ng kaunti ang sakit na nararamdaman niya at nakapag-exercise na uli si nanay.',
                'evaluation' => 'Nagpasalamat at natuwa ako kay nanay dahil nakakabangon na ito at nakakapaglakad-lakad na. Hindi katulad ng dati, dapat lang lagi sa kanyang tabi ang kanyang assistive device para maiwasan ang pagkatumba ni nanay. Lapitan si nanay kung makikipag usap sa kanya. Dapat lakasan ang boses at dun mismo sa tabi ng tenga niya imimik para marinig at masagot agad niya ang itatanong at sasabihin sa kanya. Kailangan ng mahabang pasensya at malawak na pang-unawa sa kalagayan ni nanay. Kailangan titingnan-tingan si anay dahil nawawala na rin siya minsan sa tamang pag-iisip, matinding pang-unawa at mahabang pagpapasenya ng kailangan ibigay kay nanay. Dapat ipagpasalamat at naging ok na siya. Naging maikli na uli ang mga kuko ni nanay, malinis na pati ito tingan. Natuwa si nanay dahil may nakasama siya a pagpunta sa labas dahil mahina pa daw siya.'
            ],
            [
                'assessment' => 'Medyo hirap si tatay sa kanyang pag-upo at paglalakad Lalo na kung wala ang kanyang assistive device. Naikwento ni tatay na dati daw ay lagi siyang naduduwal at nagsusuka tuwing pagkatapos nyang kumain, ngayon ay hindi na Kahit marami siyang kainin, lagging nanghihinayang siya sa kanyang kinakain at inilalabas din ang pagkatapos. Isinama din daw siya ng kanyang apo sa lugar nila sa Lapinig. Matalas na at mahahaba na uli ang mga kuko ni tatay. Mainit sa loob ng Bahay nina tatay kaya niyaya ko ito sa labas na magpahangin at magpasikat ng araw.',
                'evaluation' => 'Nahihirapan parin pero nasiguro namang ligtas o safe si tatay sa pagtumba. Pinayuhan ko si tatay na kailangan niya magbakasyon kung isasama siya ng kanyang apo para maiba naman ang paligid niya at baka doon ay maalagaan din siya ng anak lalaki. Naging makinis at maikli na uli ang mga kuko ni tatay. Napreskuhan si tatay sa labas. Hindi na kinailangang magpaypay o mag-electric fan dahil mahangin sa labas.'
            ],
        ];

        for ($i = 0; $i < 4; $i++) {
            $weeklyCarePlanId = DB::table('weekly_care_plans')->insertGetId([
                'beneficiary_id' => $beneficiaryIds[$i],
                'care_worker_id' => $careWorkerId,
                'vital_signs_id' => $vitalSignsIds[$i],
                'date' => Carbon::now()->subDays(rand(1, 30)),
                'assessment' => $assessmentEvaluationPairs[$i]['assessment'],
                'evaluation_recommendations' => $assessmentEvaluationPairs[$i]['evaluation'],
                'created_by' => $careWorkerId,
                'updated_by' => $careWorkerId,
                'illnesses' => $i % 2 == 0 ? 'Hypertension, Arthritis' : 'Diabetes, Mataas na blood pressure',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'photo_path' => 'default-care-plan.jpg', // Add default photo path
            ], 'weekly_care_plan_id');
            
            // Add 2-4 interventions for each care plan
            $numInterventions = rand(2, 4);
            for ($j = 0; $j < $numInterventions; $j++) {
                // Randomly decide if this is a standard or custom intervention
                $isCustom = (rand(0, 1) == 1);
                
                if ($isCustom) {
                    // Custom intervention
                    $customDescriptions = [
                        'Pagtulong sa paglakad papunta sa banyo',
                        'Pagbasa ng libro o dyaryo sa beneficiary',
                        'Pagsisigurong umiinom ng sapat na tubig',
                        'Pagsabay sa paglalakad sa labas ng bahay',
                        'Pagtulong sa pagbibihis',
                        'Pag-assist sa paggamit ng assistive device',
                        'Pag-monitor sa pag-inom ng gamot',
                        'Pagpapalagay ng mga bagong linen sa kama'
                    ];
                    
                    DB::table('weekly_care_plan_interventions')->insert([
                        'weekly_care_plan_id' => $weeklyCarePlanId,
                        'intervention_id' => null,
                        'care_category_id' => $careCategories[array_rand($careCategories)], 
                        'intervention_description' => $customDescriptions[array_rand($customDescriptions)],
                        'duration_minutes' => rand(10, 60),
                        'implemented' => (rand(0, 1) == 1),
                    ]);
                } else {
                    // Standard intervention
                    DB::table('weekly_care_plan_interventions')->insert([
                        'weekly_care_plan_id' => $weeklyCarePlanId,
                        'intervention_id' => $interventions[array_rand($interventions)],
                        'care_category_id' => null,
                        'intervention_description' => null,
                        'duration_minutes' => rand(15, 120),
                        'implemented' => (rand(0, 1) == 1),
                    ]);
                }
            }
        }
    }
};