<?php
// database/factories/VisitationOccurrenceFactory.php
namespace Database\Factories;

use App\Models\VisitationOccurrence;
use App\Models\Visitation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitationOccurrenceFactory extends Factory
{
    protected $model = VisitationOccurrence::class;
    
    // Realistic completion notes
    protected $completionNotes = [
        'Visit completed as scheduled. Beneficiary in stable condition.',
        'All care tasks completed successfully. Beneficiary reports feeling well.',
        'Provided all scheduled care. Noted slight improvement in mobility.',
        'Completed visit and documented vital signs in care log.',
        'Assisted with personal care and medication management as planned.',
        'Visit completed. Observed slight confusion that resolved during visit.',
        'Provided care as scheduled. Family member present during visit.',
        'Completed all tasks on care plan. No concerns to report.',
        'Visit successful. Beneficiary participating well in care activities.',
        'Care provided as scheduled. Left care summary for family.'
    ];
    
    // Realistic cancellation notes
    protected $cancellationNotes = [
        'Visit canceled due to beneficiary having medical appointment on the same day.',
        'Canceled due to family request - beneficiary visiting relatives.',
        'Visit rescheduled due to care worker illness.',
        'Canceled as beneficiary was admitted to hospital yesterday.',
        'Visit canceled due to transportation issues in reaching remote location.',
        'Rescheduled due to typhoon warning in the area.',
        'Family requested cancellation as they will be out of town.',
        'Canceled due to beneficiary not feeling well and preferring to rest.',
        'Rescheduled as beneficiary had unexpected visitors from out of town.',
        'Visit postponed due to COVID-19 symptoms in household.'
    ];

    public function definition()
    {
        // Find a visitation or create one
        $visitation = Visitation::inRandomOrder()->first();
        if (!$visitation) {
            $visitation = Visitation::factory()->create();
        }
        
        // Keep as Carbon object for comparison
        $occurrenceDateCarbon = Carbon::parse($visitation->visitation_date);
        // Format as string only when saving to DB
        $occurrenceDate = $occurrenceDateCarbon->format('Y-m-d');
        
        $startTime = $visitation->start_time;
        $endTime = $visitation->end_time;
        
        // Determine status based on date - using Carbon object for comparison
        $status = 'scheduled';
        if ($occurrenceDateCarbon->isPast()) {
            $status = $this->faker->randomElement(['completed', 'canceled', 'completed', 'completed']); // weight towards completed
        } elseif ($occurrenceDateCarbon->isToday()) {
            $status = $this->faker->randomElement(['scheduled', 'in_progress', 'completed']);
        }
        
        // Generate appropriate notes based on status
        $notes = null;
        if ($status === 'completed') {
            $notes = $this->faker->randomElement($this->completionNotes);
        } elseif ($status === 'canceled') {
            $notes = $this->faker->randomElement($this->cancellationNotes);
        }
        
        return [
            'visitation_id' => $visitation->visitation_id,
            'occurrence_date' => $occurrenceDate, // String format for DB
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $status,
            'is_modified' => $this->faker->boolean(10), // 10% chance of being modified
            'notes' => $notes,
        ];
    }
    
    /**
     * Indicate that the occurrence is completed.
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'notes' => $this->faker->randomElement($this->completionNotes),
            ];
        });
    }
    
    /**
     * Indicate that the occurrence is canceled.
     */
    public function canceled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'canceled',
                'notes' => $this->faker->randomElement($this->cancellationNotes),
            ];
        });
    }
}