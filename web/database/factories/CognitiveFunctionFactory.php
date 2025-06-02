<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CognitiveFunction;

class CognitiveFunctionFactory extends Factory
{
    protected $model = CognitiveFunction::class;

    // Realistic memory assessments for elderly
    protected $memoryAssessments = [
        'Long-term memory remains intact with vivid recollection of childhood events, but shows difficulty remembering recent conversations and appointments',
        'Memory for routines and familiar tasks is preserved, but struggles with learning new information or recalling recent events',
        'Exhibits occasional name-finding difficulties and misplaces items around the house',
        'Recent memory impairment requiring written reminders for medications and appointments',
        'Memory generally intact with age-appropriate forgetfulness, primarily affecting names of new acquaintances',
        'Demonstrates good recall of significant life events but inconsistent memory for day-to-day activities',
        'Shows selective memory patterns, with strong recall of emotionally significant events but gaps in neutral information',
        'Memory fluctuates throughout the day, typically better in mornings and deteriorating by evening',
        'Requires occasional prompting for self-care routines but remembers important family dates',
        'Exhibits confabulation to fill memory gaps, particularly when discussing recent events'
    ];

    // Realistic thinking skills assessments
    protected $thinkingSkillsAssessments = [
        'Problem-solving abilities remain strong for familiar situations but shows difficulty adapting to new challenges',
        'Abstract thinking is preserved, though processing speed has noticeably slowed',
        'Demonstrates good judgment in practical matters but may need additional time for complex decisions',
        'Shows occasional difficulty following multi-step instructions or complex conversations',
        'Reasoning abilities intact but exhibits decreased cognitive flexibility when plans change',
        'Mathematical abilities well-preserved, especially for calculations related to household budgeting',
        'Demonstrates good insight into own limitations but occasionally overestimates physical capabilities',
        'Shows intact logical reasoning but struggles with tasks requiring divided attention',
        'Critical thinking remains strong when addressing family matters but shows decreased initiative in problem-solving',
        'Decision-making becomes more cautious and deliberate, occasionally leading to analysis paralysis',
        'Maintains wisdom and perspective in advising younger family members'
    ];

    // Realistic orientation assessments
    protected $orientationAssessments = [
        'Fully oriented to person, place, and time, with occasional confusion about exact dates',
        'Well-oriented within familiar environments but may become disoriented in new settings',
        'Temporal orientation fluctuates, particularly regarding day of the week and exact time of day',
        'Maintains awareness of current location and personal identity but occasionally confused about the current year',
        'Orientation to time weakens in the evenings (sundowning effect), but remains stable during daylight hours',
        'Recognizes family members and close friends consistently, but may confuse names of infrequent visitors',
        'Demonstrates reliable orientation within home environment but becomes anxious in unfamiliar surroundings',
        'Occasionally confuses past events with present, particularly when discussing deceased family members',
        'Recognizes significant national events and holidays but may misjudge how recently they occurred',
        'Maintains orientation to self and immediate family but sometimes confused about relationships to extended family'
    ];

    // Realistic behavior assessments
    protected $behaviorAssessments = [
        'Maintains consistent daily routine with predictable behavior patterns',
        'Shows increased irritability when tired or during disruptions to established routine',
        'Exhibits appropriate social behavior but may become withdrawn in overstimulating environments',
        'Demonstrates occasional stubbornness regarding healthcare recommendations',
        'Shows anxiety when separated from primary caregiver or when left alone for extended periods',
        'Exhibits occasional restlessness in the late afternoon or early evening hours',
        'Maintains appropriate emotional regulation with occasional brief periods of tearfulness',
        'Demonstrates strong adherence to cultural customs and traditions',
        'Shows occasional hoarding behaviors with items of personal significance',
        'Exhibits heightened sensitivity to perceived disrespect from younger family members',
        'Maintains strong religious practices and routines that provide behavioral structure',
        'Shows occasional perseveration on specific concerns, particularly health or financial issues'
    ];

    /**
     * Define the model's default state with realistic cognitive function data.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'general_care_plan_id' => 1, // This will be set in the seeder
            'memory' => $this->faker->randomElement($this->memoryAssessments),
            'thinking_skills' => $this->faker->randomElement($this->thinkingSkillsAssessments),
            'orientation' => $this->faker->randomElement($this->orientationAssessments),
            'behavior' => $this->faker->randomElement($this->behaviorAssessments)
        ];
    }
}