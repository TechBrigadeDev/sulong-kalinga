<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role_id = $this->faker->randomElement([1, 2, 3]);
        $organization_role_id = $role_id == 1 ? $this->faker->randomElement([2, 3]) : null;
        $municipalityId = $this->faker->randomElement([1, 2]);
        
        // Care manager ID will be set later for care workers in the DatabaseSeeder
        $assigned_care_manager_id = null;

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'birthday' => $this->faker->date(),
            'civil_status' => $this->faker->randomElement(['Single', 'Married', 'Widowed']),
            'educational_background' => $this->faker->randomElement(['High School', 'College', 'Graduate']),
            'mobile' => '+63' . $this->faker->numerify('##########'),
            'landline' => $this->faker->unique()->numerify('########'),
            'personal_email' => $this->faker->unique()->safeEmail,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('12312312'), // Set the password to '12312312' and hash it
            'address' => $this->faker->address,
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'religion' => $this->faker->randomElement(['Christian', 'Muslim', 'Other']),
            'nationality' => 'Filipino',
            'volunteer_status' => 'Active',
            'status_start_date' => now(),
            'status_end_date' => now()->addYears(1),
            'role_id' => $role_id,
            'status' => 'Active',
            'organization_role_id' => $organization_role_id,
            'assigned_municipality_id' => $municipalityId,
            'assigned_care_manager_id' => $assigned_care_manager_id, // Added this field
            'photo' => null,
            'government_issued_id' => null,
            'sss_id_number' => $this->faker->numerify('##########'),
            'philhealth_id_number' => $this->faker->numerify('##########'),
            'pagibig_id_number' => $this->faker->numerify('##########'),
            'cv_resume' => null,
            'updated_by' => 1,
            'remember_token' => Str::random(10),
        ];
    }
}