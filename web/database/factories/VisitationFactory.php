<?php
// database/factories/VisitationFactory.php
namespace Database\Factories;

use App\Models\Visitation;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitationFactory extends Factory
{
    protected $model = Visitation::class;
    
    // Visit type-specific notes for more realistic data
    protected $visitTypeNotes = [
        'routine_care_visit' => [
            'Regular wellness check and personal care assistance for beneficiary.',
            'Scheduled care visit to monitor health status and assist with daily activities.',
            'Weekly check-in to provide personal care and assess general wellbeing.',
            'Routine health monitoring and assistance with medication management.',
            'Scheduled home visit to help with activities of daily living and assess care needs.'
        ],
        'service_request' => [
            'Special assistance requested by family for medical appointment preparation.',
            'Home visit arranged to help with special bathing needs following hospital discharge.',
            'Service requested to help organize medications into new pill organizer.',
            'Special visit to assist with mobility exercises recommended by physical therapist.',
            'Visit to help with household tasks that beneficiary can no longer manage independently.'
        ],
        'emergency_visit' => [
            'Urgent visit following family report of beneficiary experiencing increased confusion.',
            'Emergency check-in after beneficiary missed scheduled medication doses.',
            'Urgent visitation to assess reported fall incident with no apparent injury.',
            'Rapid response visit due to concerns about sudden deterioration in health status.',
            'Emergency visit to assess beneficiary following power outage affecting medical equipment.'
        ]
    ];

    public function definition()
    {
        $isFlexibleTime = $this->faker->boolean(30); // 30% chance of flexible time
        $startTime = $isFlexibleTime ? null : $this->faker->dateTimeBetween('08:00', '16:00')->format('H:i:00');
        $endTime = null;
        if (!$isFlexibleTime && $startTime) {
            // Create a realistic duration based on visit type
            $visitType = $this->faker->randomElement(['routine_care_visit', 'service_request', 'emergency_visit']);
            $durationMinutes = $this->getVisitDuration($visitType);
            $endTime = Carbon::parse($startTime)->addMinutes($durationMinutes)->format('H:i:00');
        }
        
        $careWorker = User::where('role_id', 3)->inRandomOrder()->first();
        if (!$careWorker) {
            $careWorker = User::factory()->create(['role_id' => 3]);
        }
        
        $beneficiary = Beneficiary::inRandomOrder()->first();
        if (!$beneficiary) {
            $beneficiary = Beneficiary::factory()->create();
        }
        
        $status = $this->faker->randomElement(['scheduled', 'completed', 'canceled']);
        $confirmedOn = $status === 'completed' ? $this->faker->dateTimeBetween('-1 month') : null;
        $visitType = $this->faker->randomElement(['routine_care_visit', 'service_request', 'emergency_visit']);
        
        $confirmedByBeneficiary = null;
        $confirmedByFamily = null;
        
        if ($status === 'completed') {
            if ($this->faker->boolean) {
                $confirmedByBeneficiary = $beneficiary->beneficiary_id;
            } else {
                $familyMember = FamilyMember::where('related_beneficiary_id', $beneficiary->beneficiary_id)
                    ->inRandomOrder()->first();
                if ($familyMember) {
                    $confirmedByFamily = $familyMember->family_member_id;
                }
            }
        }
        
        return [
            'care_worker_id' => $careWorker->id,
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'visit_type' => $visitType,
            'visitation_date' => $this->faker->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
            'is_flexible_time' => $isFlexibleTime,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'notes' => $this->getRealisticNoteByVisitType($visitType),
            'date_assigned' => $this->faker->dateTimeBetween('-2 months', '-1 day')->format('Y-m-d'),
            'assigned_by' => User::where('role_id', '<=', 2)->inRandomOrder()->first()->id ?? 1,
            'status' => $status,
            'confirmed_by_beneficiary' => $confirmedByBeneficiary,
            'confirmed_by_family' => $confirmedByFamily,
            'confirmed_on' => $confirmedOn,
        ];
    }
    
    /**
     * Get a realistic visit duration based on visit type
     *
     * @param string $visitType The type of visit
     * @return int Duration in minutes
     */
    private function getVisitDuration($visitType)
    {
        switch ($visitType) {
            case 'routine_care_visit':
                return $this->faker->numberBetween(45, 90); // 45-90 minutes
            case 'service_request':
                return $this->faker->numberBetween(30, 120); // 30-120 minutes
            case 'emergency_visit':
                return $this->faker->numberBetween(60, 180); // 60-180 minutes
            default:
                return $this->faker->numberBetween(30, 120); // 30-120 minutes
        }
    }
    
    /**
     * Get a realistic note based on visit type
     *
     * @param string $visitType The type of visit
     * @return string A realistic note
     */
    private function getRealisticNoteByVisitType($visitType)
    {
        if (array_key_exists($visitType, $this->visitTypeNotes)) {
            return $this->faker->randomElement($this->visitTypeNotes[$visitType]);
        }
        
        return $this->faker->sentence();
    }
}