<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Mobility;

class MobilityFactory extends Factory
{
    protected $model = Mobility::class;

    // Realistic walking ability assessments
    protected $walkingAbilities = [
        'Ambulatory with slight unsteadiness, requires occasional rest during longer walks',
        'Walks independently with cane for stability, particularly on uneven surfaces',
        'Walks short distances unassisted but requires walker for distances over 20 meters',
        'Requires assistance of one person for safe ambulation due to weakness in right leg',
        'Independent with walker but experiences increased difficulty during rainy season due to joint pain',
        'Ambulatory within home environment but requires wheelchair for community mobility',
        'Unsteady gait with tendency to lean to the left side requiring close supervision',
        'Can walk independently but tires quickly, limiting continuous walking to 10-15 minutes',
        'Shuffling gait with small steps and decreased arm swing, at risk for falls',
        'Walks with slow, deliberate movements and widened base of support for stability',
        'Requires handrails or wall support when navigating steps or uneven terrain',
        'Experiences intermittent freezing of gait, especially when starting to walk or turning',
        'Walking ability fluctuates throughout day, typically strongest in mid-morning'
    ];

    // Realistic assistive devices information
    protected $assistiveDevices = [
        'Uses wooden cane with rubber tip for outdoor walking',
        'Requires four-wheeled walker with seat for community mobility',
        'Uses quad cane for morning ambulation when joint stiffness is prominent',
        'Has adjustable-height walker with front wheels and stationary back legs',
        'Uses traditional wooden bakya slippers indoors for familiarity despite recommendations for more supportive footwear',
        'Alternates between single-point cane and holding onto furniture for indoor mobility',
        'Requires wheelchair for distances beyond 50 meters but resists consistent use',
        'Uses forearm crutches for stable weight-bearing after recent hip fracture',
        'Has grab bars installed in bathroom and beside bed for transfer assistance',
        'Uses cane outdoors but refuses assistive devices within home environment',
        'Recently upgraded to rolling walker with hand brakes and seat for rest periods',
        'Uses locally-made bamboo walking stick that family has reinforced with rubber grip',
        'Has transfer bench for bathing and raised toilet seat with handrails',
        'Keeps multiple assistive devices throughout home to ensure one is always within reach'
    ];

    // Realistic transportation needs
    protected $transportationNeeds = [
        'Requires tricycle for local travel, with assistance transferring in and out',
        'Can use jeepney for transportation if someone accompanies to help with steps',
        'Needs door-to-door service, preferably with familiar driver who understands mobility limitations',
        'Can navigate public transportation with supervision but avoids rush hour travel',
        'Requires vehicle with high seat for easier transfers; cannot manage low-seated cars',
        'Prefers motorcycle with sidecar (tricycle) for local transportation with family escort',
        'Needs assistance folding and storing walker when using public transportation',
        'Can only tolerate short trips (under 30 minutes) due to discomfort from sitting',
        'Requires front seat positioning in vehicles with good legroom for comfort',
        'Avoids travel during rainy season due to increased fall risk and joint pain',
        'Needs transportation with air conditioning during hot weather to prevent fatigue',
        'Relies on family members for all transportation needs; cannot independently use public options',
        'Benefits from portable cushion when traveling to reduce discomfort',
        'Requires vehicle with enough space for caregiver to accompany closely'
    ];

    /**
     * Define the model's default state with realistic mobility data.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'general_care_plan_id' => 1, // This will be set in the seeder
            'walking_ability' => $this->faker->randomElement($this->walkingAbilities),
            'assistive_devices' => $this->faker->randomElement($this->assistiveDevices),
            'transportation_needs' => $this->faker->randomElement($this->transportationNeeds)
        ];
    }
}