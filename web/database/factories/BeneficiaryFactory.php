<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\Barangay;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BeneficiaryFactory extends Factory
{
    protected $model = Beneficiary::class;
    
    // Filipino first names (mixed gender)
    protected $maleFirstNames = [
        'Antonio', 'Jose', 'Manuel', 'Francisco', 'Juan', 'Roberto', 'Ricardo', 'Eduardo', 
        'Carlos', 'Miguel', 'Rafael', 'Felix', 'Alejandro', 'Victor', 'Rodrigo', 'Ernesto', 
        'Fernando', 'Danilo', 'Mariano', 'Pedro', 'Rene', 'Domingo', 'Cesar', 'Jaime', 'Efren',
        'Nestor', 'Romeo', 'Alfredo', 'Luis', 'Ramon', 'Felipe', 'Salvador', 'Enrique', 'Benjamin',
        'Raul', 'Alfonso', 'Arsenio', 'Virgilio', 'Mario', 'Alberto', 'Ismael', 'Guillermo', 'Emilio',
        'Leonardo', 'Vicente', 'Rolando', 'Arturo', 'Crisanto', 'Diosdado'
    ];

    protected $femaleFirstNames = [
        'Maria', 'Rosario', 'Carmen', 'Gloria', 'Teresita', 'Josefina', 'Luzviminda', 'Corazon', 
        'Fe', 'Lourdes', 'Erlinda', 'Imelda', 'Nida', 'Clarita', 'Leticia', 'Flordeliza', 
        'Carmelita', 'Remedios', 'Anita', 'Lilia', 'Esperanza', 'Milagros', 'Felicidad', 'Virginia', 
        'Yolanda', 'Lolita', 'Marilou', 'Loida', 'Celia', 'Felisa', 'Juliana', 'Elisa', 'Soledad', 
        'Juanita', 'Nenita', 'Aurora', 'Lucia', 'Delia', 'Marina', 'Consuelo', 'Estelita', 'Melinda',
        'Estrella', 'Josefa', 'Norma', 'Florentina', 'Edita', 'Priscila', 'Trinidad'
    ];

    // Filipino middle names/surnames that can be used as middle names
    protected $middleNames = [
        'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Mendoza', 'Del Rosario', 'Tolentino', 
        'Valdez', 'Ramirez', 'Navarro', 'Domingo', 'Salazar', 'Mercado', 'Espiritu', 'Villanueva', 
        'Trinidad', 'Gonzales', 'Aguilar', 'Castro', 'De Guzman', 'De Leon', 'De La Cruz', 'Pascual',
        'Estrada', 'Torres', 'Padilla', 'Sevilla', 'Concepcion', 'Ignacio', 'Galang', 'Manalo'
    ];

    // Filipino last names
    protected $lastNames = [
        'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Mendoza', 'Del Rosario', 'Garcia', 
        'Tolentino', 'Valdez', 'Ramirez', 'Navarro', 'Domingo', 'Salazar', 'Mercado', 'Espiritu', 
        'Villanueva', 'Ramos', 'Castro', 'Rivera', 'Torres', 'Gonzales', 'Aguilar', 'De Guzman', 
        'De Leon', 'De La Cruz', 'Rodriguez', 'Francisco', 'Gonzaga', 'Jacinto', 'Lim', 'Manalaysay',
        'Morales', 'Pascual', 'Padilla', 'Robles', 'Rosario', 'Tan', 'Vergara', 'Yap', 'Diaz', 
        'Flores', 'Fernandez', 'Aquino', 'Enriquez', 'Santiago', 'Soriano', 'Fajardo', 'Valencia',
        'Pangilinan', 'Gomez', 'Samson', 'David', 'Alvarez', 'Dizon', 'Hernandez'
    ];

    // Street patterns for Northern Samar
    protected $streetPatterns = [
        'Purok %d, Barangay %s',
        'Sitio %s, Barangay %s',
        '%d %s Street, Barangay %s',
        'Zone %d, Barangay %s',
        'Lot %d, Block %d, Barangay %s',
        'Phase %d, Barangay %s'
    ];

    // Common street names
    protected $streetNames = [
        'Rizal', 'Bonifacio', 'Mabini', 'Aguinaldo', 'Luna', 'Del Pilar', 'Jacinto', 'Abad Santos',
        'Burgos', 'National', 'Provincial', 'Magsaysay', 'Quezon', 'Laurel', 'Quirino', 'Roxas',
        'Garcia', 'Marcos', 'Aquino', 'Ramos', 'Estrada', 'Arroyo', 'Macapagal', 'Osmeña',
        'J.P. Laurel', 'Ninoy Aquino', 'Lapu-Lapu', 'Kalayaan', 'Katipunan', 'Tandang Sora'
    ];

    // Sitio names
    protected $sitioNames = [
        'Malipayon', 'Masagana', 'Maligaya', 'Malinis', 'Makisig', 'Matiwasay', 'Maharlika',
        'Mapalad', 'Mapayapa', 'Manggahan', 'Maunlad', 'Mainit', 'Mataas', 'Mabuhay', 'Bagong Simula',
        'Bagong Pag-asa', 'Bagong Buhay', 'Kamalayan', 'Kaunlaran', 'Kasaganaan'
    ];

    // Relation types for emergency contacts
     protected $relationTypes = [
        'Child' => ['Son', 'Daughter'],
        'Spouse' => ['Husband', 'Wife'],
        'Sibling' => ['Brother', 'Sister'],
        'Cousin' => ['Cousin'],
        'NieceNephew' => ['Niece', 'Nephew'],
        'Grandchild' => ['Grandson', 'Granddaughter'],
        'Neighbor' => ['Neighbor'],
        'Friend' => ['Friend'],
        'Caregiver' => ['Caregiver']
    ];
    
    // Map of barangays for each municipality
    protected $barangayMap = [
        // Mondragon (Municipality ID 1) - Barangay IDs 1-24
        1 => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
        // San Roque (Municipality ID 2) - Barangay IDs 25-40
        2 => [25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40]
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Gender: default to Male, but will be overridden by seeder
            'gender' => 'Male',

            // Use a closure so $attributes['gender'] is available
            'first_name' => function (array $attributes) {
                $gender = $attributes['gender'] ?? 'Male';
                return $this->faker->randomElement(
                    $gender === 'Male' ? $this->maleFirstNames : $this->femaleFirstNames
                );
            },
            'middle_name' => $this->faker->randomElement($this->middleNames),
            'last_name' => $this->faker->randomElement($this->lastNames),
            'civil_status' => $this->faker->randomElement(['Single', 'Married', 'Widowed', 'Separated']),
            'birthday' => $this->faker->dateTimeBetween('-100 years', '-60 years')->format('Y-m-d'),

            // Municipality and barangay
            'municipality_id' => function () {
                return $this->faker->randomElement([1, 2]);
            },
            'barangay_id' => function (array $attributes) {
                $municipalityId = $attributes['municipality_id'] ?? 1;
                return $this->faker->randomElement($this->barangayMap[$municipalityId]);
            },
            'street_address' => function (array $attributes) {
                $barangayId = $attributes['barangay_id'] ?? 1;
                $barangay = \App\Models\Barangay::find($barangayId);
                $barangayName = $barangay ? $barangay->barangay_name : 'Barangay';
                return $this->generateAddress($barangayName);
            },

            // Username (first initial + middle initial + last name, unique)
            'username' => function (array $attributes) {
                $firstName = $attributes['first_name'] ?? 'A';
                $middleName = $attributes['middle_name'] ?? 'Santos';
                $lastName = $attributes['last_name'] ?? 'Santos';
                $baseUsername = strtolower(substr($firstName, 0, 1) . substr($middleName, 0, 1) . $lastName);
                $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername);
                return $this->generateUniqueUsername($baseUsername);
            },

            // Password
            'password' => \Illuminate\Support\Facades\Hash::make('12312312'),

            // Primary caregiver (70% chance, opposite gender)
            'primary_caregiver' => function (array $attributes) {
                if ($this->faker->boolean(70)) {
                    $gender = $attributes['gender'] ?? 'Male';
                    $oppositeGender = $gender === 'Male' ? 'Female' : 'Male';
                    return $this->generateFilipinoName($oppositeGender);
                }
                return null;
            },

            // Contact info
            'mobile' => '+63' . $this->faker->numberBetween(9000000000, 9999999999),
            'landline' => $this->faker->boolean(70) ? $this->faker->numberBetween(2000000, 9999999) : null,

            // Category
            'category_id' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8]),

            // Emergency contact
            'emergency_contact_name' => function (array $attributes) {
                $gender = $attributes['gender'] ?? 'Male';
                $emergencyContactGender = $this->faker->boolean(65) ? ($gender === 'Male' ? 'Female' : 'Male') : $gender;
                $firstName = $this->faker->randomElement(
                    $emergencyContactGender === 'Male' ? $this->maleFirstNames : $this->femaleFirstNames
                );
                $lastName = $this->faker->randomElement($this->lastNames);
                return $firstName . ' ' . $lastName;
            },
            'emergency_contact_relation' => function () {
                $relationKey = array_rand($this->relationTypes);
                $relationOptions = $this->relationTypes[$relationKey];
                return $relationOptions[array_rand($relationOptions)];
            },
            'emergency_contact_mobile' => '+63' . $this->faker->numberBetween(9000000000, 9999999999),
            'emergency_contact_email' => function (array $attributes) {
                if ($this->faker->boolean(60)) {
                    $name = $attributes['emergency_contact_name'] ?? 'contact';
                    return strtolower(str_replace(' ', '.', $name)) . '@' .
                        $this->faker->randomElement(['gmail.com', 'yahoo.com', 'outlook.com']);
                }
                return null;
            },

            // Emergency procedure
            'emergency_procedure' => $this->faker->randomElement([
                "Contact family immediately and provide medical care.",
                "Call emergency services and administer prescribed medication.",
                "Check vital signs, provide medication, and notify care manager.",
                "Contact the designated emergency contact and care worker simultaneously.",
                "Assist with breathing exercises, administer medication if needed, and call family.",
                "Keep calm, check for immediate dangers, and call emergency services.",
                "Follow the emergency response protocol outlined in the care plan.",
                "Ensure patient is comfortable, monitor vital signs, and contact medical professional.",
                "Administer first aid if qualified, then contact emergency services.",
                "Contact the nearest health center while monitoring the patient's condition."
            ]),

            // Status and care plan
            'beneficiary_status_id' => 1,
            'status_reason' => null,
            'general_care_plan_id' => null,

            // Signatures
            'beneficiary_signature' => null,
            'care_worker_signature' => null,

            // Created/updated by
            'created_by' => function () {
                return \App\Models\User::where('role_id', 2)->inRandomOrder()->first()->id;
            },
            'updated_by' => function (array $attributes) {
                return $attributes['created_by'] ?? \App\Models\User::where('role_id', 2)->inRandomOrder()->first()->id;
            },

            // Timestamps
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    /**
     * Generate a realistic address in the specified barangay
     */
    protected function generateAddress($barangayName)
    {
        $pattern = $this->faker->randomElement($this->streetPatterns);
        
        if (strpos($pattern, 'Sitio') !== false) {
            return sprintf($pattern, $this->faker->randomElement($this->sitioNames), $barangayName);
        } elseif (strpos($pattern, 'Street') !== false) {
            return sprintf($pattern, $this->faker->numberBetween(1, 99), 
                          $this->faker->randomElement($this->streetNames), $barangayName);
        } elseif (strpos($pattern, 'Block') !== false) {
            return sprintf($pattern, $this->faker->numberBetween(1, 20), 
                          $this->faker->numberBetween(1, 50), $barangayName);
        } else {
            return sprintf($pattern, $this->faker->numberBetween(1, 10), $barangayName);
        }
    }
    
    /**
     * Generate a realistic Filipino name
     */
    protected function generateFilipinoName($gender = null)
    {
        if (!$gender) {
            $gender = $this->faker->randomElement(['Male', 'Female']);
        }
        
       if ($gender === 'Male') {
            $firstName = $this->faker->randomElement($this->maleFirstNames);
        } else {
            $firstName = $this->faker->randomElement($this->femaleFirstNames);
        }
        
        $lastName = $this->faker->randomElement($this->lastNames);
        
        return $firstName . ' ' . $lastName;
    }

    /**
     * Generate a unique username, adding a number if duplicates exist
     */
    protected function generateUniqueUsername($baseUsername)
    {
        // Check if username exists
        $count = Beneficiary::where('username', 'like', $baseUsername . '%')
                            ->count();
        
        // If no duplicates, return the base username
        if ($count === 0) {
            return $baseUsername;
        }
        
        // If duplicates exist, add a number (count + 1)
        return $baseUsername . ($count + 1);
    }
}