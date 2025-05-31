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
    protected $firstNames = [
        // Male names
        'Antonio', 'Jose', 'Manuel', 'Francisco', 'Juan', 'Roberto', 'Ricardo', 'Eduardo', 
        'Carlos', 'Miguel', 'Rafael', 'Felix', 'Alejandro', 'Victor', 'Rodrigo', 'Ernesto', 
        'Fernando', 'Danilo', 'Mariano', 'Pedro', 'Rene', 'Domingo', 'Cesar', 'Jaime', 'Efren',
        'Nestor', 'Romeo', 'Alfredo', 'Luis', 'Ramon', 'Felipe', 'Salvador', 'Enrique', 'Benjamin',
        'Raul', 'Alfonso', 'Arsenio', 'Virgilio', 'Mario', 'Alberto', 'Ismael', 'Guillermo', 'Emilio',
        'Leonardo', 'Vicente', 'Rolando', 'Arturo', 'Crisanto', 'Diosdado',
        
        // Female names
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
        // Determine gender and select appropriate name
        $gender = $this->faker->randomElement(['Male', 'Female']);
        
        // Select first name based on gender
        if ($gender === 'Male') {
            $firstName = $this->faker->randomElement(array_slice($this->firstNames, 0, 49)); // First 49 are male names
        } else {
            $firstName = $this->faker->randomElement(array_slice($this->firstNames, 49)); // Rest are female names
        }
        
        $middleName = $this->faker->randomElement($this->middleNames);
        $lastName = $this->faker->randomElement($this->lastNames);
        
        // Get care manager for created_by and updated_by
        $userIdWithRole2 = User::where('role_id', 2)->inRandomOrder()->first()->id;
        
        // 50% for each municipality
        $municipalityId = $this->faker->randomElement([1, 2]); 
        
        // Pick a barangay from the appropriate municipality
        $barangayId = $this->faker->randomElement($this->barangayMap[$municipalityId]);
        $barangay = Barangay::find($barangayId);
        $barangayName = $barangay->barangay_name;
        
        // Generate an address that includes the barangay name
        $streetAddress = $this->generateAddress($barangayName);
        
        // Generate a birthday for someone 60-100 years old
        $birthday = $this->faker->dateTimeBetween('-100 years', '-60 years')->format('Y-m-d');
        
        // Generate username (first initial + middle initial + last name)
        $baseUsername = strtolower(substr($firstName, 0, 1) . substr($middleName, 0, 1) . $lastName);
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername); // Remove special characters

         // Check for username duplicates and add a number if needed
        $username = $this->generateUniqueUsername($baseUsername);
        
        // Generate emergency contact (slightly more likely to be of opposite gender)
        $emergencyContactGender = $this->faker->boolean(65) ? ($gender === 'Male' ? 'Female' : 'Male') : $gender;
        
        if ($emergencyContactGender === 'Male') {
            $emergencyContactName = $this->faker->randomElement(array_slice($this->firstNames, 0, 49)) . ' ' . 
                                  $this->faker->randomElement($this->lastNames);
        } else {
            $emergencyContactName = $this->faker->randomElement(array_slice($this->firstNames, 49)) . ' ' . 
                                  $this->faker->randomElement($this->lastNames);
        }
        
        // Select a relation type
        $relationKey = array_rand($this->relationTypes);
        $relationOptions = $this->relationTypes[$relationKey];
        $relation = $relationOptions[array_rand($relationOptions)];
        
        // Generate emergency procedure
        $emergencyProcedures = [
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
        ];
        
        return [
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'civil_status' => $this->faker->randomElement(['Single', 'Married', 'Widowed', 'Separated']),
            'gender' => $gender,
            'birthday' => $birthday,
            'primary_caregiver' => $this->faker->boolean(70) ? $this->generateFilipinoName($gender === 'Male' ? 'Female' : 'Male') : null,
            'mobile' => $this->faker->boolean(80) ? '+63' . $this->faker->numberBetween(9000000000, 9999999999) : null,
            'landline' => $this->faker->boolean(30) ? $this->faker->numberBetween(2000000, 9999999) : null,
            'street_address' => $streetAddress,
            'barangay_id' => $barangayId,
            'municipality_id' => $municipalityId,
            'category_id' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8]),
            'emergency_contact_name' => $emergencyContactName,
            'emergency_contact_relation' => $relation,
            'emergency_contact_mobile' => '+63' . $this->faker->numberBetween(9000000000, 9999999999),
            'emergency_contact_email' => $this->faker->boolean(60) ? 
                                      strtolower(str_replace(' ', '.', $emergencyContactName)) . '@' . 
                                      $this->faker->randomElement(['gmail.com', 'yahoo.com', 'outlook.com']) : null,
            'emergency_procedure' => $this->faker->randomElement($emergencyProcedures),
            'beneficiary_status_id' => 1, // Default to Active
            'status_reason' => null,
            'general_care_plan_id' => null, // Will be set after creation
            'username' => $username,
            'password' => Hash::make('12312312'), // Default password
            'beneficiary_signature' => null,
            'care_worker_signature' => null,
            'created_by' => $userIdWithRole2,
            'updated_by' => $userIdWithRole2,
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
            $firstName = $this->faker->randomElement(array_slice($this->firstNames, 0, 49));
        } else {
            $firstName = $this->faker->randomElement(array_slice($this->firstNames, 49));
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