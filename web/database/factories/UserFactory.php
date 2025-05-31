<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Barangay;

class UserFactory extends Factory
{
    protected $model = User::class;

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

    // Filipino educational backgrounds
    protected $educationalBackgrounds = [
        'Elementary Graduate',
        'High School Undergraduate',
        'High School Graduate',
        'Vocational/Technical Course',
        'College Undergraduate',
        'Bachelor\'s Degree',
        'Master\'s Degree',
        'Doctorate Degree'
    ];

    // Filipino religions
    protected $religions = [
        'Roman Catholic',
        'Iglesia ni Cristo',
        'Protestant',
        'Born Again Christian',
        'Seventh-day Adventist',
        'Islam',
        'Jehovah\'s Witness',
        'Baptist',
        'Methodist',
        'Other Christian Denomination',
        'Traditional Indigenous Beliefs'
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
        $role_id = $this->faker->randomElement([1, 2, 3]);
        $organization_role_id = $role_id == 1 ? $this->faker->randomElement([2, 3]) : null;
        $municipalityId = $this->faker->randomElement([1, 2]);
        
        // Care manager ID will be set later for care workers in the DatabaseSeeder
        $assigned_care_manager_id = null;
        
        // Determine gender
        $gender = $this->faker->randomElement(['Male', 'Female']);
        
        // Select first name based on gender
        if ($gender === 'Male') {
            $firstName = $this->faker->randomElement(array_slice($this->firstNames, 0, 49)); // First 49 are male names
        } else {
            $firstName = $this->faker->randomElement(array_slice($this->firstNames, 49)); // Rest are female names
        }
        
        $lastName = $this->faker->randomElement($this->lastNames);
        
        // Generate a proper Philippine mobile number
        $mobile = '+63' . $this->faker->numberBetween(9000000000, 9999999999);
        
        // Generate a realistic landline format for Philippines (7-digit)
        $landline = $this->faker->numberBetween(2000000, 8999999);
        
        // Generate birthday (18-65 years old for staff)
        $birthday = $this->faker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d');
        
        // Pick a barangay from the appropriate municipality (for address only, not storing the ID)
        $barangayId = $this->faker->randomElement($this->barangayMap[$municipalityId]);
        $barangay = Barangay::find($barangayId);
        $barangayName = $barangay ? $barangay->barangay_name : 'Unknown Barangay';
        
        // Generate an address that includes the barangay name
        $address = $this->generateAddress($barangayName);
        
        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'birthday' => $birthday,
            'civil_status' => $this->faker->randomElement(['Single', 'Married', 'Widowed', 'Separated', 'Divorced']),
            'educational_background' => $this->faker->randomElement($this->educationalBackgrounds),
            'mobile' => $mobile,
            'landline' => $landline,
            'personal_email' => strtolower(str_replace(' ', '.', $firstName)) . '.' . 
                              strtolower(str_replace(' ', '.', $lastName)) . '@gmail.com',
            'email' => strtolower(str_replace(' ', '.', $firstName)) . '.' . 
                     strtolower(str_replace(' ', '.', $lastName)) . '@cose.org.ph',
            'password' => Hash::make('12312312'), // Set the password to '12312312' and hash it
            'address' => $address, // Address now fully contains the barangay information
            // 'barangay_id' => $barangayId, // Removed as it doesn't exist in PostgreSQL schema
            'gender' => $gender,
            'religion' => $this->faker->randomElement($this->religions),
            'nationality' => 'Filipino',
            'volunteer_status' => 'Active',
            'status_start_date' => now()->subMonths(random_int(1, 24)),
            'status_end_date' => now()->addYears(1),
            'role_id' => $role_id,
            'status' => 'Active',
            'organization_role_id' => $organization_role_id,
            'assigned_municipality_id' => $municipalityId,
            'assigned_care_manager_id' => $assigned_care_manager_id,
            'photo' => null,
            'government_issued_id' => null,
            'sss_id_number' => $this->faker->numerify('##-#######-#'),
            'philhealth_id_number' => $this->faker->numerify('##-#########-#'),
            'pagibig_id_number' => $this->faker->numerify('####-####-####'),
            'cv_resume' => null,
            'updated_by' => 1,
            'remember_token' => Str::random(10),
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
}