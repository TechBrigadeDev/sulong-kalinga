<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\PortalAccount;

class PortalAccountFactory extends Factory
{
    protected $model = PortalAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'portal_email' => $this->faker->unique()->safeEmail,
            'portal_password' => Hash::make('12312312'), // Set the password to '12312312' and hash it
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}