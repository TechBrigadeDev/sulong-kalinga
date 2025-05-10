<?php

namespace Database\Factories;

use App\Models\MobileUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MobileUserFactory extends Factory
{
    protected $model = MobileUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('12312312'), // Using the same default password as your other factories
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'mobile' => '+63' . $this->faker->numerify('##########'),
            'role_id' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'status' => 'Active',
            'user_type' => $this->faker->randomElement(['cose', 'portal']),
            'cose_user_id' => null,
            'portal_account_id' => null,
        ];
    }

    /**
     * Configure the model factory for COSE staff.
     */
    public function coseStaff(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'user_type' => 'cose',
                'role_id' => $this->faker->randomElement([1, 2, 3]),
                'cose_user_id' => null,
                'portal_account_id' => null,
            ];
        });
    }

    /**
     * Configure the model factory for portal users (beneficiaries and family members).
     */
    public function portalUser(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'user_type' => 'portal',
                'role_id' => $this->faker->randomElement([4, 5]),
                'cose_user_id' => null,
            ];
        });
    }
}