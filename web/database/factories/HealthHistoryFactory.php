<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\HealthHistory;

class HealthHistoryFactory extends Factory
{
    protected $model = HealthHistory::class;

    /**
     * Define the model's default state with realistic health data.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Realistic medical conditions for elderly
        $medicalConditions = [
            'Dementia',
            'Alzheimer\'s Disease',
            'Parkinson\'s Disease',
            'Arthritis',
            'Osteoporosis',
            'Hypertension',
            'Diabetes Mellitus',
            'Coronary Artery Disease',
            'Chronic Obstructive Pulmonary Disease',
            'Heart Failure',
            'Stroke',
            'Cataracts',
            'Age-related Macular Degeneration',
            'Hearing Loss',
            'Depression',
            'Anxiety Disorder',
            'Malnutrition',
            'Urinary Incontinence'
        ];
        
        // Select 1-5 medical conditions randomly
        $selectedConditions = $this->faker->randomElements(
            $medicalConditions,
            $this->faker->numberBetween(1, 5)
        );
        
        // Realistic medications for elderly
        $medicationOptions = [
            'Amlodipine 5mg daily',
            'Metformin 500mg twice daily',
            'Atorvastatin 10mg at night',
            'Aspirin 81mg daily',
            'Lisinopril 10mg daily',
            'Metoprolol 25mg twice daily',
            'Furosemide 20mg daily',
            'Levothyroxine 50mcg daily',
            'Donepezil 5mg at night',
            'Sertraline 50mg daily',
            'Gabapentin 300mg three times daily',
            'Pantoprazole 40mg daily',
            'Albuterol inhaler 2 puffs as needed',
            'Memantine 10mg daily',
            'Warfarin 5mg daily'
        ];
        
        // Select 2-7 medications randomly
        $selectedMedications = $this->faker->randomElements(
            $medicationOptions,
            $this->faker->numberBetween(2, 7)
        );
        
        // Realistic allergies
        $allergyOptions = [
            'Penicillin',
            'Sulfa drugs',
            'NSAIDs',
            'Shellfish',
            'Eggs',
            'Peanuts',
            'Tree nuts',
            'Latex',
            'Contrast dye',
            'No known allergies'
        ];
        
        // Select 0-2 allergies randomly or "No known allergies"
        $selectedAllergies = [];
        if ($this->faker->boolean(70)) {
            $selectedAllergies = $this->faker->randomElements(
                array_slice($allergyOptions, 0, -1), // Exclude "No known allergies"
                $this->faker->numberBetween(0, 2)
            );
        } else {
            $selectedAllergies[] = 'No known allergies';
        }
        
        // Realistic immunizations for elderly
        $immunizationOptions = [
            'Influenza vaccine (annual)',
            'Pneumococcal vaccine (PPSV23)',
            'Pneumococcal vaccine (PCV13)',
            'Tetanus/Diphtheria (Td) booster',
            'Shingles vaccine (Shingrix)',
            'Hepatitis B vaccine'
        ];
        
        // Select 1-4 immunizations randomly
        $selectedImmunizations = $this->faker->randomElements(
            $immunizationOptions,
            $this->faker->numberBetween(1, 4)
        );
        
        return [
            'general_care_plan_id' => 1, // This will be set in the seeder
            'medical_conditions' => json_encode($selectedConditions),
            'medications' => json_encode($selectedMedications),
            'allergies' => json_encode($selectedAllergies),
            'immunizations' => json_encode($selectedImmunizations)
        ];
    }
}