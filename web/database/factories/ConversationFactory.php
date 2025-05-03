<?php

namespace Database\Factories;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Conversation::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => null, // For one-on-one conversations
            'is_group_chat' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    /**
     * Configure the model factory as a group chat.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function groupChat(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => $this->faker->words(2, true) . ' Group', // Generate a group name
                'is_group_chat' => true,
            ];
        });
    }
    
    /**
     * Configure the model factory for a private chat between two users.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function privateChat(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => null,
                'is_group_chat' => false,
            ];
        });
    }
}