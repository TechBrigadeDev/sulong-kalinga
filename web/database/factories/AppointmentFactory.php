<?php
// database/factories/AppointmentFactory.php
namespace Database\Factories;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Appointment::class;
    
    /**
     * Type-specific realistic appointment titles
     */
    protected $appointmentTitles = [
        'Quarterly Feedback Sessions' => [
            'Q2 Feedback and Performance Review Session',
            'Quarterly Performance Evaluation and Growth Planning',
            'Q3 Staff Feedback and Development Discussion',
            'End-of-Year Performance Review and Goal Setting',
            'Program Evaluation and Staff Feedback Session'
        ],
        'Skills Enhancement Training' => [
            'Advanced Wound Care Management Training',
            'Effective Communication with Dementia Patients',
            'Medication Management Best Practices Workshop',
            'Transfer Techniques and Fall Prevention Training',
            'Crisis Intervention and Emergency Response Training'
        ],
        'Municipal Development Council Participation' => [
            'Municipal Development Council Monthly Planning Meeting',
            'MDC Budget Allocation for Elderly Care Programs',
            'Development Council Review of Healthcare Programs',
            'MDC Quarterly Strategic Planning Session',
            'Municipal Council Presentation on Elder Care Needs'
        ],
        'Municipal Local Health Board Meeting' => [
            'Local Health Board Quarterly Review Meeting',
            'Health Board Discussion on Chronic Disease Management',
            'Municipal Health Strategies Coordination Meeting',
            'Health Board Resource Allocation Planning',
            'Community Health Program Evaluation Session'
        ],
        'Liga Meeting' => [
            'Liga ng mga Barangay Monthly Coordination Meeting',
            'Liga Planning for Community Health Programs',
            'Inter-Barangay Healthcare Coordination Session',
            'Liga Elderly Support Program Planning',
            'Barangay Health Workers Coordination Meeting'
        ],
        'HMO Referral' => [
            'HMO Care Coordination for High-Risk Beneficiaries',
            'Health Insurance Coverage Review Meeting',
            'Medical Referral Systems Improvement Discussion',
            'HMO Partnership Program Evaluation',
            'Healthcare Provider Network Expansion Planning'
        ],
        'Assessment and Review of Care Needs' => [
            'Comprehensive Care Needs Assessment Review',
            'Multi-disciplinary Care Planning Session',
            'Complex Case Review and Care Planning',
            'Care Needs Standardization Working Group',
            'Assessment Tools Improvement Workshop'
        ],
        'General Care Plan Finalization' => [
            'Care Plan Finalization and Implementation Review',
            'Inter-professional Care Planning Conference',
            'Care Plan Standards and Quality Improvement',
            'End-of-Quarter Care Plan Review Session',
            'Care Plan Documentation and Compliance Workshop'
        ],
        'Project Team Meetings' => [
            'Community Outreach Project Planning Meeting',
            'Digital Documentation System Implementation Team',
            'Elderly Wellness Program Development Committee',
            'Quality Improvement Initiative Working Group',
            'Program Sustainability Planning Team'
        ],
        'Mentoring and Feedback Sessions' => [
            'New Care Worker Mentoring Program Session',
            'Professional Development and Career Path Planning',
            'Peer Support and Knowledge Sharing Circle',
            'Clinical Skills Development Feedback Session',
            'Leadership Development for Care Managers'
        ],
        'Others' => [
            'Inter-agency Coordination for Disaster Preparedness',
            'Budget Planning for Next Fiscal Year',
            'Community Partner Relationship Building Event',
            'Staff Wellness and Stress Management Workshop',
            'Technology Adoption and Training Session'
        ]
    ];
    
    /**
     * Type-specific realistic descriptions
     */
    protected $appointmentDescriptions = [
        'Quarterly Feedback Sessions' => [
            'Review staff performance metrics, discuss areas of strength and improvement, and create professional development plans for the next quarter.',
            'Conduct formal evaluations of care delivery quality, address performance concerns, and recognize outstanding contributions to beneficiary care.',
            'Discuss program outcomes, staff challenges, and opportunities for improvement in service delivery. Individual staff progress will be reviewed.',
            'End of period review of care metrics, documentation quality, and adherence to standard procedures. Staff will receive personalized feedback.',
            'Team-based performance evaluation with focus on interdisciplinary collaboration and communication effectiveness.'
        ],
        'Skills Enhancement Training' => [
            'Focused training on advanced skills needed for complex care situations. Participants will demonstrate practical competence by the end of session.',
            'Skill development workshop with hands-on practice sessions. Each participant will receive individual coaching on technique improvement.',
            'Evidence-based practice training to ensure all staff are implementing current best practices in elder care and chronic disease management.',
            'Mandatory certification renewal training with assessment component. All staff must demonstrate proficiency to maintain active status.',
            'Specialized skills development for managing complex behavioral issues and supporting beneficiaries with cognitive impairments.'
        ],
        'Municipal Development Council Participation' => [
            'Discuss integration of elderly care services with broader municipal development plans. Present data on community needs and service gaps.',
            'Advocate for increased budget allocation to support expanded elderly care programs in underserved barangays of Northern Samar.',
            'Present outcomes of current elder care initiatives and propose enhancements to the municipal development framework.',
            'Participate in strategic planning for healthcare infrastructure development needed to support growing elderly population.',
            'Coordinate with other sectors to ensure elderly care is incorporated into disaster preparedness and climate resilience planning.'
        ],
        'Municipal Local Health Board Meeting' => [
            'Present elderly health data trends and discuss coordination of primary healthcare services with specialized elder care.',
            'Review community health indicators relevant to elderly population and develop strategies to address emerging health challenges.',
            'Coordinate integration of elder care referral systems with municipal health network to ensure seamless service delivery.',
            'Discuss medical supply chain challenges affecting elderly care and propose solutions for sustainable access to essential medications.',
            'Develop protocols for emergency healthcare response specifically designed for vulnerable elderly populations in remote areas.'
        ],
        'Liga Meeting' => [
            'Coordinate with barangay captains to strengthen grassroots support for elder care services and identification of at-risk elderly.',
            'Present updates on elder care program implementation across different barangays and share best practices for community engagement.',
            'Discuss challenges in service delivery at barangay level and develop collaborative solutions for transportation and access issues.',
            'Establish consistent reporting mechanisms for barangay health workers to monitor elderly welfare between formal care visits.',
            'Plan coordinated community information campaigns on elder abuse prevention and reporting across all participating barangays.'
        ],
        'HMO Referral' => [
            'Review cases requiring specialized medical intervention beyond program scope and coordinate referrals to appropriate providers.',
            'Meet with HMO representatives to streamline authorization processes for elderly beneficiaries needing specialized care.',
            'Evaluate patterns in referral needs to identify gaps in current service provision and potential program enhancements.',
            'Discuss complex cases requiring multi-specialty care and develop coordinated care plans with HMO case managers.',
            'Review denied claims and appeals processes to improve access to necessary services for program beneficiaries.'
        ],
        'Assessment and Review of Care Needs' => [
            'Conduct interdisciplinary review of complex cases to ensure comprehensive care planning addresses all identified needs.',
            'Standardize assessment protocols across the program to ensure consistency in care need identification and prioritization.',
            'Evaluate effectiveness of current assessment tools for capturing culturally-specific needs of Filipino elderly population.',
            'Review trends in care needs data to inform program development and resource allocation for coming fiscal period.',
            'Train staff on enhanced assessment techniques for identifying non-verbal cues and subtle changes in beneficiary condition.'
        ],
        'General Care Plan Finalization' => [
            'Review draft care plans for new beneficiaries and ensure alignment with assessment findings before implementation.',
            'Evaluate implementation challenges of existing care plans and make necessary modifications to improve effectiveness.',
            'Standardize documentation practices across care teams to ensure consistent quality and completeness of care plans.',
            'Integrate family caregiver feedback into formal care plans to create more sustainable and holistic support systems.',
            'Review care plan outcomes data to identify successful intervention patterns that can be replicated across the program.'
        ],
        'Project Team Meetings' => [
            'Coordinate implementation timelines for new elder care initiatives and assign team responsibilities for key deliverables.',
            'Review project milestones, address implementation barriers, and adjust strategies to ensure successful program roll-out.',
            'Evaluate preliminary data from pilot programs and determine requirements for scaling successful interventions.',
            'Plan community engagement strategies to increase program visibility and encourage participation of isolated elderly.',
            'Develop monitoring frameworks for new initiatives to ensure rigorous evaluation of outcomes and impact measurement.'
        ],
        'Mentoring and Feedback Sessions' => [
            'Provide structured guidance to new care workers through case review and reflective practice discussion.',
            'Facilitate knowledge transfer between experienced staff and newer team members through guided shadowing debrief.',
            'Review challenging client interactions and provide constructive feedback on communication and intervention strategies.',
            'Support professional development through individualized goal setting and progress review for career advancement.',
            'Build leadership capacity through mentoring relationships that focus on both technical and management skill development.'
        ],
        'Others' => [
            'Address administrative and operational matters requiring cross-functional collaboration outside regular meeting structure.',
            'Respond to emerging community needs or crisis situations that require rapid program adaptation and resource mobilization.',
            'Conduct specialized planning sessions for program expansion, major policy changes, or structural reorganization.',
            'Facilitate external partnerships with NGOs, academic institutions, or government agencies for resource leveraging.',
            'Develop innovative approaches to elder care challenges through design thinking and collaborative problem-solving sessions.'
        ]
    ];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startTime = $this->faker->dateTimeBetween('08:00', '16:00')->format('H:i:s');
        $endTime = Carbon::parse($startTime)->addMinutes($this->faker->randomElement([30, 60, 90, 120]))->format('H:i:s');
        $isFlexibleTime = $this->faker->boolean(20); // 20% chance of being flexible time
        
        // Get a random appointment type
        $appointmentType = AppointmentType::inRandomOrder()->first();
        if (!$appointmentType) {
            $appointmentType = AppointmentType::factory()->create();
        }
        
        // Get type name
        $typeName = $appointmentType->type_name;
        
        // Generate a realistic title based on type
        $title = isset($this->appointmentTitles[$typeName])
            ? $this->faker->randomElement($this->appointmentTitles[$typeName])
            : $this->faker->sentence(4);
            
        // Generate a realistic description based on type
        $description = isset($this->appointmentDescriptions[$typeName])
            ? $this->faker->randomElement($this->appointmentDescriptions[$typeName])
            : $this->faker->paragraph();
        
        return [
            'appointment_type_id' => $appointmentType->appointment_type_id,
            'title' => $title,
            'description' => $description,
            'other_type_details' => $typeName === 'Others' ? $this->faker->sentence() : null,
            'date' => $this->faker->dateTimeBetween('+1 days', '+2 months'),
            'start_time' => $isFlexibleTime ? null : $startTime,
            'end_time' => $isFlexibleTime ? null : $endTime,
            'is_flexible_time' => $isFlexibleTime,
            'meeting_location' => $this->getMeetingLocation($typeName),
            'status' => 'scheduled',
            'notes' => $this->faker->optional(70)->paragraph(),
            'created_by' => User::where('role_id', '<=', 2)->inRandomOrder()->first()->id ?? 1,
            'updated_by' => null
        ];
    }

    /**
     * Generate appropriate meeting location based on meeting type
     */
    private function getMeetingLocation($typeName)
    {
        switch ($typeName) {
            case 'Municipal Development Council Participation':
            case 'Municipal Local Health Board Meeting':
            case 'Liga Meeting':
                return $this->faker->randomElement([
                    'Municipal Hall Conference Room',
                    'Municipal Health Office',
                    'Municipal Council Chambers', 
                    'Community Center'
                ]);
                
            case 'Skills Enhancement Training':
                return $this->faker->randomElement([
                    'Training Center',
                    'Multi-purpose Hall',
                    'Municipal Health Office Training Room',
                    'Provincial Training Center'
                ]);
                
            default:
                return $this->faker->randomElement([
                    'Main Office Conference Room',
                    'Field Office',
                    'Program Center',
                    'Virtual Meeting (Zoom)',
                    'Care Center Meeting Room',
                    'Health Center Conference Room'
                ]);
        }
    }
}