<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear existing appointment types before adding new ones
        DB::table('appointment_types')->delete();
        
        $types = [
            ['type_name' => 'Quarterly Feedback Sessions', 'color_code' => '#4e73df', 'description' => 'Regular feedback sessions held quarterly'],
            ['type_name' => 'Skills Enhancement Training', 'color_code' => '#1cc88a', 'description' => 'Training sessions to enhance staff skills'],
            ['type_name' => 'Municipal Development Council Participation', 'color_code' => '#36b9cc', 'description' => 'Participation in municipal development council meetings'],
            ['type_name' => 'Municipal Local Health Board Meeting', 'color_code' => '#f6c23e', 'description' => 'Meetings with the municipal local health board'],
            ['type_name' => 'Liga Meeting', 'color_code' => '#e74a3b', 'description' => 'Liga ng mga Barangay meetings'],
            ['type_name' => 'HMO Referral', 'color_code' => '#6f42c1', 'description' => 'Health maintenance organization referral discussions'],
            ['type_name' => 'Assessment and Review of Care Needs', 'color_code' => '#fd7e14', 'description' => 'Assessing and reviewing beneficiary care needs'],
            ['type_name' => 'General Care Plan Finalization', 'color_code' => '#20c997', 'description' => 'Finalizing care plans for beneficiaries'],
            ['type_name' => 'Project Team Meetings', 'color_code' => '#3949ab', 'description' => 'Meetings with project teams'],
            ['type_name' => 'Mentoring and Feedback Sessions', 'color_code' => '#ec407a', 'description' => 'Sessions for mentoring and providing feedback'],
            ['type_name' => 'Others', 'color_code' => '#a435f0', 'description' => 'Other meeting types not categorized above'],
        ];

        foreach ($types as $type) {
            DB::table('appointment_types')->insertOrIgnore($type);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to delete data when rolling back
    }
};