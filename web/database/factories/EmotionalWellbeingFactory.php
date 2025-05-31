<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EmotionalWellbeing;

class EmotionalWellbeingFactory extends Factory
{
    protected $model = EmotionalWellbeing::class;

    // Realistic mood patterns for elderly Filipinos
    protected $moods = [
        'Generally positive with occasional sadness when reminiscing about late spouse',
        'Exhibits normal mood fluctuations, typically more cheerful in the mornings',
        'Shows periods of withdrawal and melancholy, especially during rainy weather',
        'Predominantly anxious about health issues and family concerns',
        'Frequently expresses nostalgia and longing for "better times"',
        'Generally content but becomes frustrated with physical limitations',
        'Emotional state is stable but shows signs of subdued depression',
        'Mood remains positive when surrounded by family, particularly grandchildren',
        'Experiences emotional ups and downs linked to physical comfort level',
        'Shows anxiety about being a burden to family members',
        'Exhibits seasonal mood changes, more withdrawn during typhoon season',
        'Displays irritability when routine is disrupted',
        'Shows resilient spirit despite health challenges',
        'Expresses satisfaction with life accomplishments but worries about unfinished responsibilities'
    ];

    // Realistic social interaction patterns
    protected $socialInteractions = [
        'Enjoys daily conversations with neighbors during afternoon "tambay" sessions',
        'Prefers one-on-one interactions with close family members over group settings',
        'Actively participates in weekly church/parish activities and prayer groups',
        'Maintains close relationships with 3-4 longtime friends who visit regularly',
        'Enjoys telling stories to grandchildren but tires quickly in extended family gatherings',
        'Remains socially engaged through barangay senior citizen activities',
        'Prefers to observe rather than participate in large family celebrations',
        'Maintains connections primarily through phone calls with distant relatives',
        'Enjoys visits from former colleagues and workplace friends',
        'Shows increased social withdrawal in recent months, declining invitations',
        'Relies heavily on daily interaction with primary caregiver for social stimulation',
        'Engages enthusiastically with visitors but needs rest afterward',
        'Maintains strong bonds with siblings who check in regularly',
        'Participates in community feeding programs as both recipient and volunteer when able'
    ];

    // Realistic emotional support needs
    protected $emotionalSupportNeeds = [
        'Needs regular reassurance about not being a burden to family',
        'Benefits from reminiscence therapy and opportunities to share life stories',
        'Requires gentle encouragement to express feelings of loss and grief',
        'Needs consistent validation of concerns about health changes',
        'Benefits from spiritual support and regular prayer time with family',
        'Requires patience and understanding when discussing sensitive topics like finances',
        'Needs opportunities to feel useful and contribute to household in small ways',
        'Benefits from regular updates about extended family to maintain connections',
        'Needs predictable daily routine to minimize anxiety',
        'Would benefit from companionship during doctor visits to manage medical anxiety',
        'Requires calm, unhurried communication style to prevent frustration',
        'Benefits from regular affirmation of self-worth beyond physical capabilities',
        'Needs regular opportunities for intergenerational interaction',
        'Would benefit from culturally appropriate counseling to address adjustment issues'
    ];

    /**
     * Define the model's default state with realistic emotional wellbeing data.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'general_care_plan_id' => 1, // This will be set in the seeder
            'mood' => $this->faker->randomElement($this->moods),
            'social_interactions' => $this->faker->randomElement($this->socialInteractions),
            'emotional_support_needs' => $this->faker->randomElement($this->emotionalSupportNeeds)
        ];
    }
}