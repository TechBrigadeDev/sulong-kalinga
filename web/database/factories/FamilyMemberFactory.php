<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FamilyMember;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FamilyMemberFactory extends Factory
{
    protected $model = FamilyMember::class;

    // Filipino first names (mixed gender)
    protected $firstNames = [
        // Male names
        'Antonio', 'Jose', 'Manuel', 'Francisco', 'Juan', 'Roberto', 'Ricardo', 'Eduardo', 
        'Carlos', 'Miguel', 'Rafael', 'Felix', 'Alejandro', 'Victor', 'Rodrigo', 'Ernesto', 
        'Fernando', 'Danilo', 'Mariano', 'Pedro', 'Rene', 'Domingo', 'Cesar', 'Jaime', 'Efren',
        'Nestor', 'Romeo', 'Alfredo', 'Luis', 'Ramon', 'Felipe', 'Salvador', 'Enrique', 'Benjamin',
        // Female names
        'Maria', 'Rosario', 'Carmen', 'Gloria', 'Teresita', 'Josefina', 'Luzviminda', 'Corazon', 
        'Fe', 'Lourdes', 'Erlinda', 'Imelda', 'Nida', 'Clarita', 'Leticia', 'Flordeliza', 
        'Carmelita', 'Remedios', 'Anita', 'Lilia', 'Esperanza', 'Milagros', 'Felicidad', 'Virginia'
    ];

    // Filipino last names
    protected $lastNames = [
        'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Mendoza', 'Del Rosario', 'Garcia', 
        'Tolentino', 'Valdez', 'Ramirez', 'Navarro', 'Domingo', 'Salazar', 'Mercado', 'Espiritu', 
        'Villanueva', 'Ramos', 'Castro', 'Rivera', 'Torres', 'Gonzales', 'Aguilar', 'De Guzman', 
        'De Leon', 'De La Cruz', 'Rodriguez', 'Francisco', 'Gonzaga', 'Jacinto', 'Lim', 'Manalaysay',
        'Morales', 'Pascual', 'Padilla', 'Robles', 'Rosario'
    ];

    // Filipino family relationships (culturally appropriate)
    protected $relationships = [
        'Anak' => 10, // Child
        'Apo' => 8,   // Grandchild
        'Pamangkin' => 5, // Niece/Nephew
        'Pinsan' => 5,   // Cousin
        'Kapatid' => 15,  // Sibling
        'Bayaw/Hipag' => 3, // Brother/Sister-in-law
        'Manugang' => 3,   // Son/Daughter-in-law
        'Biyenan' => 2,    // Parent-in-law
        'Asawa' => 12,     // Spouse
        'Inaanak' => 2,    // Godchild
        'Ninong/Ninang' => 2, // Godparent
        'Kapitbahay' => 1, // Neighbor (sometimes treated as family)
        'Kaibigan' => 1,   // Friend (sometimes treated as family)
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get the care manager IDs for created_by and updated_by
        $userIdWithRole2 = User::where('role_id', 2)->inRandomOrder()->first()->id;
        
        // Determine gender
        $gender = $this->faker->randomElement(['Male', 'Female']);
        
        // Select first name based on gender
        if ($gender === 'Male') {
            $firstName = $this->faker->randomElement(array_slice($this->firstNames, 0, 34)); // First 34 are male names
        } else {
            $firstName = $this->faker->randomElement(array_slice($this->firstNames, 34)); // Rest are female names
        }
        
        $lastName = $this->faker->randomElement($this->lastNames);
        
        // Generate a birthday for family member (18-75 years old)
        $birthday = $this->faker->dateTimeBetween('-75 years', '-18 years')->format('Y-m-d');
        
        // Weighted selection for relationship
        $relationship = $this->weightedRandomRelationship();
        
        // Generate a proper Philippine mobile number
        $mobile = '+63' . $this->faker->numberBetween(9000000000, 9999999999);
        
        // Generate a realistic landline format for Philippines (7-digit)
        $landline = $this->faker->numberBetween(2000000, 8999999);
        
        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'birthday' => $birthday,
            'mobile' => $mobile,
            'landline' => $landline,
            'email' => $this->faker->unique()->safeEmail,
            // Hash the password properly
            'password' => Hash::make('12312312'), // Default password that can be changed later
            'street_address' => $this->faker->address,
            'gender' => $gender,
            'related_beneficiary_id' => function () {
                return Beneficiary::inRandomOrder()->first()->beneficiary_id;
            },
            'relation_to_beneficiary' => $relationship,
            'is_primary_caregiver' => $relationship === 'Anak' || $relationship === 'Asawa' ? 
                                      $this->faker->boolean(70) : $this->faker->boolean(20),
            'created_by' => $userIdWithRole2,
            'updated_by' => $userIdWithRole2,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    /**
     * Configure the model factory to associate with a specific beneficiary
     * 
     * @param int $beneficiaryId
     * @return $this
     */
    public function forBeneficiary($beneficiaryId)
    {
        return $this->state(function (array $attributes) use ($beneficiaryId) {
            // Get the beneficiary to copy the address
            $beneficiary = Beneficiary::find($beneficiaryId);
            
            // Family members commonly share the last name and address with beneficiary
            return [
                'related_beneficiary_id' => $beneficiaryId,
                'last_name' => $this->faker->boolean(70) ? $beneficiary->last_name : $attributes['last_name'],
                'street_address' => $this->faker->boolean(60) ? $beneficiary->street_address : $attributes['street_address'],
            ];
        });
    }
    
    /**
     * Weighted random selection of relationship type
     *
     * @return string
     */
    protected function weightedRandomRelationship()
    {
        $total = array_sum($this->relationships);
        $rand = mt_rand(1, $total);
        
        $sum = 0;
        foreach ($this->relationships as $relationship => $weight) {
            $sum += $weight;
            if ($rand <= $sum) {
                return $relationship;
            }
        }
        
        // Fallback
        return array_key_first($this->relationships);
    }
}