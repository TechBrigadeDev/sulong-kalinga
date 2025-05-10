<?php
// database/factories/VisitationFactory.php
namespace Database\Factories;

use App\Models\Visitation;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitationFactory extends Factory
{
    protected $model = Visitation::class;

    public function definition()
    {
        $isFlexibleTime = $this->faker->boolean(30); // 30% chance of flexible time
        $startTime = $isFlexibleTime ? null : $this->faker->dateTimeBetween('08:00', '16:00')->format('H:i:00');
        $endTime = $isFlexibleTime ? null : $this->faker->dateTimeBetween($startTime, '17:00')->format('H:i:00');
        
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
            'visit_type' => $this->faker->randomElement(['routine_care_visit', 'service_request', 'emergency_visit']),
            'visitation_date' => $this->faker->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
            'is_flexible_time' => $isFlexibleTime,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'notes' => $this->faker->text,
            'date_assigned' => $this->faker->dateTimeBetween('-2 months', '-1 day')->format('Y-m-d'),
            'assigned_by' => User::where('role_id', '<=', 2)->inRandomOrder()->first()->id ?? 1,
            'status' => $status,
            'confirmed_by_beneficiary' => $confirmedByBeneficiary,
            'confirmed_by_family' => $confirmedByFamily,
            'confirmed_on' => $confirmedOn,
        ];
    }
}