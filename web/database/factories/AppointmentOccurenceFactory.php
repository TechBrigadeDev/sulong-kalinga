<?php
// database/factories/AppointmentOccurrenceFactory.php
namespace Database\Factories;

use App\Models\AppointmentOccurrence;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentOccurrenceFactory extends Factory
{
    protected $model = AppointmentOccurrence::class;
    
    // Realistic meeting notes
    protected $completionNotes = [
        'Meeting completed successfully. All agenda items covered. Follow-up tasks assigned.',
        'Session was productive with high participation. Action items documented and assigned.',
        'Training completed with all staff demonstrating competency in required skills.',
        'Meeting outcomes achieved. Minutes will be circulated for review.',
        'Successful discussion with clear next steps identified. Follow-up scheduled.',
        'All participants attended and contributed to discussions. Key decisions documented.',
        'Meeting objectives met. New strategies approved for implementation next quarter.',
        'Training session completed with positive feedback from participants.',
        'Productive discussion leading to consensus on key program directions.',
        'Important decisions made regarding resource allocation. Implementation plan created.'
    ];
    
    // Realistic cancellation notes
    protected $cancellationNotes = [
        'Meeting canceled due to key participants being unavailable. Will reschedule soon.',
        'Canceled due to scheduling conflict with urgent municipal meeting.',
        'Session postponed due to insufficient preparation time. New date to be announced.',
        'Canceled due to emergency situation requiring immediate staff attention.',
        'Meeting rescheduled to allow more time for data collection and analysis.',
        'Postponed at request of municipal officials. Awaiting new schedule.',
        'Canceled due to typhoon warning. Will reschedule once weather clears.',
        'Training session rescheduled due to trainer illness.',
        'Canceled due to low registration. Will revise approach and reschedule.',
        'Meeting postponed to incorporate newly received program guidelines.'
    ];

    public function definition()
    {
        // Find an appointment or create one
        $appointment = Appointment::inRandomOrder()->first();
        if (!$appointment) {
            $appointment = Appointment::factory()->create();
        }
        
        // Get the date and times from the parent appointment
        $occurrenceDate = Carbon::parse($appointment->date);
        $startTime = $appointment->start_time;
        $endTime = $appointment->end_time;
        
        // Determine status based on date
        $status = 'scheduled';
        if ($occurrenceDate->isPast()) {
            $status = $this->faker->randomElement(['completed', 'canceled', 'completed', 'completed']); // weight towards completed
        }
        
        // Generate appropriate notes based on status
        $notes = null;
        if ($status === 'completed') {
            $notes = $this->faker->randomElement($this->completionNotes);
        } elseif ($status === 'canceled') {
            $notes = $this->faker->randomElement($this->cancellationNotes);
        }
        
        return [
            'appointment_id' => $appointment->appointment_id,
            'occurrence_date' => $occurrenceDate->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $status,
            'is_modified' => $this->faker->boolean(10), // 10% chance of being modified
            'notes' => $notes,
        ];
    }
}