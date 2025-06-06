<?php

namespace App\Http\Controllers\Api\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FaqApiController extends Controller
{
    /**
     * Return FAQ content for the Family Portal.
     */
    public function index(Request $request)
    {
        // FAQ content based on FAQuestions.blade.php
        $faqs = [
            [
                'question' => 'What is the Mobile Healthcare Service (MHCS)?',
                'answer' => 'The Mobile Healthcare Service (MHCS) is a program by the Coalition of Services for the Elderly (COSE) that brings essential healthcare services directly to elderly beneficiaries in their homes. We currently operate in 34 barangays across Northern Samar, serving seniors aged 60 and above who may have difficulty accessing traditional healthcare facilities.'
            ],
            [
                'question' => 'Who is eligible for MHCS services?',
                'answer' => 'Our services are available to senior citizens aged 60 and above residing in our service areas (currently Mondragon and San Roque in Northern Samar). We prioritize those with chronic illnesses, disabilities, or limited mobility who face challenges accessing regular healthcare facilities.'
            ],
            [
                'question' => 'What services does MHCS provide?',
                'answer' => 'Our comprehensive services include: Regular health monitoring (blood pressure, temperature, etc.); Assistance with medication management; Basic hygiene care and personal assistance; Mobility support and physical therapy guidance; Nutritional guidance and meal assistance; Referrals to hospitals or specialists when needed; Emotional support and social engagement activities.'
            ],
            [
                'question' => 'How often will the healthcare workers visit?',
                'answer' => 'Visit frequency is determined based on individual care plans. Most beneficiaries receive weekly visits, but those with more critical needs may receive more frequent care. Our team develops personalized care plans for each beneficiary to ensure their specific needs are met.'
            ],
            [
                'question' => 'Is there any cost for these services?',
                'answer' => 'MHCS services are provided free of charge to eligible beneficiaries as part of COSE\'s mission to support marginalized senior citizens. Some specialized medications or treatments may require additional arrangements, which our care workers will discuss with you if needed.'
            ],
            [
                'question' => 'Are your care workers qualified?',
                'answer' => 'All our care workers undergo rigorous training in elderly care, including home care techniques, massage therapy, and basic medical assistance. They are supervised by healthcare professionals and receive continuous education to maintain high service standards.'
            ],
            [
                'question' => 'How is my personal health information protected?',
                'answer' => 'We maintain strict confidentiality of all health records. Information is only shared with your consent or when medically necessary with other healthcare providers. Our documentation system tracks care while protecting your privacy.'
            ],
            [
                'question' => 'What should I do in case of a medical emergency?',
                'answer' => 'In emergencies, please contact local emergency services immediately. You can also notify your assigned care worker who can help coordinate with appropriate medical facilities. We recommend keeping emergency contacts readily available at all times.'
            ],
            [
                'question' => 'How can family members be involved in the care process?',
                'answer' => 'Family involvement is encouraged! You can: Participate in care plan discussions; Provide updates on your loved one\'s condition; Learn basic care techniques from our workers; Join scheduled family support sessions; Volunteer with our program.'
            ],
            [
                'question' => 'Does COSE offer other support besides healthcare?',
                'answer' => 'Yes! COSE provides various programs including: Community health facilities (Botika Binhi pharmacies, wellness centers); Livelihood programs and income-generating activities; Social engagement opportunities through Older Persons Organizations; Advocacy for senior citizens\' rights; Residential care for abandoned elderly at our Group Home in Bulacan.'
            ],
            [
                'question' => 'Still have questions?',
                'answer' => 'Contact our MHCS team at: Phone: [Insert COSE contact number] | Email: [Insert COSE email] | Or visit your local Older Persons Organization (OPO)'
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $faqs
        ]);
    }
}
