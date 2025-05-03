<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\PortalAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BeneficiaryFactory extends Factory
{
    protected $model = Beneficiary::class;

    // Store portal account IDs that have been assigned
    protected static $usedPortalAccountIds = [];
    
    // Store mapping of beneficiary IDs to portal account IDs
    protected static $beneficiaryToPortalMap = [];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $generalCarePlanId = 1;

        $municipalityId = $this->faker->randomElement([1, 2]);
        $barangayId = $municipalityId == 1 ? $this->faker->numberBetween(1, 24) : $this->faker->numberBetween(1, 16);

        // Get a random user ID with role_id 2 (Care Manager)
        $userIdWithRole2 = User::where('role_id', 2)->inRandomOrder()->first()->id;

        // Generate a birthday for someone 60-100 years old
        $birthday = $this->faker->dateTimeBetween('-100 years', '-60 years')->format('Y-m-d');

        // Get an unused portal account
        $portalAccount = $this->getUnusedPortalAccount();
        
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'civil_status' => $this->faker->randomElement(['Single', 'Married', 'Widowed']),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'birthday' => $birthday,
            'primary_caregiver' => $this->faker->name,
            'mobile' => '+63' . $this->faker->numerify('##########'),
            'landline' => $this->faker->numerify('#######'),
            'street_address' => $this->faker->address,
            'barangay_id' => $barangayId,
            'municipality_id' => $municipalityId,
            'category_id' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8]),
            'emergency_contact_name' => $this->faker->name,
            'emergency_contact_relation' => $this->faker->randomElement(['Sister', 'Brother', 'Parent']),
            'emergency_contact_mobile' => '+63' . $this->faker->numerify('##########'), 
            'emergency_contact_email' => $this->faker->unique()->safeEmail,
            'emergency_procedure' => $this->faker->sentence,
            'beneficiary_status_id' => 1,
            'status_reason' => 'N/A',
            'general_care_plan_id' => $generalCarePlanId++,
            'portal_account_id' => $portalAccount->id,
            'beneficiary_signature' => null,
            'care_worker_signature' => null,
            'created_by' => $userIdWithRole2,
            'updated_by' => $userIdWithRole2,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    /**
     * Get an unused portal account
     * 
     * @return \App\Models\PortalAccount
     */
    protected function getUnusedPortalAccount()
    {
        // Get all portal accounts that haven't been used yet
        $portalAccount = PortalAccount::whereNotIn('id', self::$usedPortalAccountIds)
            ->inRandomOrder()
            ->first();
            
        // If no portal accounts are available, return null to handle in seeder
        if (!$portalAccount) {
            throw new \Exception('No more available portal accounts. Create more using PortalAccountFactory.');
        }
        
        // Add to used accounts array
        self::$usedPortalAccountIds[] = $portalAccount->id;
        
        return $portalAccount;
    }
    
    /**
     * Configure the model factory to track the portal account used for a beneficiary
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Beneficiary $beneficiary) {
            // Store the mapping for later family member creation
            self::$beneficiaryToPortalMap[$beneficiary->beneficiary_id] = $beneficiary->portal_account_id;
        });
    }
    
    /**
     * Get the portal account ID for a beneficiary
     * 
     * @param int $beneficiaryId
     * @return int|null
     */
    public static function getPortalAccountForBeneficiary($beneficiaryId)
    {
        return self::$beneficiaryToPortalMap[$beneficiaryId] ?? null;
    }
}