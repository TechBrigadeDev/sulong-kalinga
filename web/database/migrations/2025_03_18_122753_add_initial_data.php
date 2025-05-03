<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert data into the provinces table
        DB::table('provinces')->insert([
            ['province_name' => 'Northern Samar']
        ]);

         // Insert data into the municipalities table
         DB::table('municipalities')->insert([
            ['municipality_name' => 'Mondragon', 'province_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['municipality_name' => 'San Roque', 'province_id' => 1, 'created_at' => now(), 'updated_at' => now()]
        ]);

        DB::table('barangays')->insert([
            ['barangay_name' => 'Bagasbas', 'municipality_id' => 1],
            ['barangay_name' => 'Bugko', 'municipality_id' => 1],
            ['barangay_name' => 'Cablangan', 'municipality_id' => 1],
            ['barangay_name' => 'Cagmanaba', 'municipality_id' => 1],
            ['barangay_name' => 'Cahicsan', 'municipality_id' => 1],
            ['barangay_name' => 'Chitongco', 'municipality_id' => 1],
            ['barangay_name' => 'De Maria', 'municipality_id' => 1],
            ['barangay_name' => 'Doña Lucia', 'municipality_id' => 1],
            ['barangay_name' => 'Eco', 'municipality_id' => 1],
            ['barangay_name' => 'Flormina', 'municipality_id' => 1],
            ['barangay_name' => 'Hinabangan', 'municipality_id' => 1],
            ['barangay_name' => 'Imelda', 'municipality_id' => 1],
            ['barangay_name' => 'La Trinidad', 'municipality_id' => 1],
            ['barangay_name' => 'Makiwalo', 'municipality_id' => 1],
            ['barangay_name' => 'Mirador', 'municipality_id' => 1],
            ['barangay_name' => 'Nenita', 'municipality_id' => 1],
            ['barangay_name' => 'Roxas', 'municipality_id' => 1],
            ['barangay_name' => 'San Agustin', 'municipality_id' => 1],
            ['barangay_name' => 'San Antonio', 'municipality_id' => 1],
            ['barangay_name' => 'San Isidro', 'municipality_id' => 1],
            ['barangay_name' => 'San Jose', 'municipality_id' => 1],
            ['barangay_name' => 'San Juan', 'municipality_id' => 1],
            ['barangay_name' => 'Santa Catalina', 'municipality_id' => 1],
            ['barangay_name' => 'Talolora', 'municipality_id' => 1],
            ['barangay_name' => 'Balnasan', 'municipality_id' => 2],
            ['barangay_name' => 'Balud', 'municipality_id' => 2],
            ['barangay_name' => 'Bantayan', 'municipality_id' => 2],
            ['barangay_name' => 'Coroconog', 'municipality_id' => 2],
            ['barangay_name' => 'Dale', 'municipality_id' => 2],
            ['barangay_name' => 'Ginagdanan', 'municipality_id' => 2],
            ['barangay_name' => 'Lao-angan', 'municipality_id' => 2],
            ['barangay_name' => 'Lawaan', 'municipality_id' => 2],
            ['barangay_name' => 'Malobago', 'municipality_id' => 2],
            ['barangay_name' => 'Pagsang-an', 'municipality_id' => 2],
            ['barangay_name' => 'Zone 1', 'municipality_id' => 2],
            ['barangay_name' => 'Zone 2', 'municipality_id' => 2],
            ['barangay_name' => 'Zone 3', 'municipality_id' => 2],
            ['barangay_name' => 'Zone 4', 'municipality_id' => 2],
            ['barangay_name' => 'Zone 5', 'municipality_id' => 2],
            ['barangay_name' => 'Zone 6', 'municipality_id' => 2]
        ]);

        // Insert data into the beneficiary_categories table
        DB::table('beneficiary_categories')->insert([
            ['category_name' => 'Bedridden'],
            ['category_name' => 'Bedridden & Living alone'],
            ['category_name' => 'Frail'],
            ['category_name' => 'Frail/PWD'],
            ['category_name' => 'Frail/PWD & Living alone'],
            ['category_name' => 'Frail/Living alone'],
            ['category_name' => 'Frail/Dementia'],
            ['category_name' => 'Dementia']
        ]);

        // Insert data into the beneficiary_status table
        DB::table('beneficiary_status')->insert([
            ['status_name' => 'Active'],
            ['status_name' => 'Inactive'],
            ['status_name' => 'Opted Out'],
            ['status_name' => 'Deceased'],
            ['status_name' => 'Hospitalized'],
            ['status_name' => 'Moved Residence'],
            ['status_name' => 'No Longer Needed Assistance']
        ]);

        // Insert data into the care_categories table
        DB::table('care_categories')->insert([
            ['care_category_name' => 'Mobility'],
            ['care_category_name' => 'Cognitive/Communication'],
            ['care_category_name' => 'Self-sustainability'],
            ['care_category_name' => 'Disease/Therapy Handling'],
            ['care_category_name' => 'Daily life/Social contact'],
            ['care_category_name' => 'Outdoor Activities'],
            ['care_category_name' => 'Household Keeping']
        ]);

        // Insert data into the roles table
        DB::table('roles')->insert([
            ['role_name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'care_manager', 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'care_worker', 'created_at' => now(), 'updated_at' => now()]
        ]);

        // Insert data into the organization_roles table
        DB::table('organization_roles')->insert([
            ['role_name' => 'executive_director', 'area' => 'executive_director'],
            ['role_name' => 'coordinator', 'area' => 'project_coordinator'],
            ['role_name' => 'coordinator', 'area' => 'meal_coordinator']
        ]);

        // Insert an administrator user with executive director organization role
        DB::table('cose_users')->insert([
            'first_name' => 'Emily',
            'last_name' => 'Beridico',
            'birthday' => '1990-01-01',
            'civil_status' => 'Single',
            'educational_background' => 'Graduate',
            'mobile' => '+639154952153 ',
            'landline' => '87096567',
            'personal_email' => 'emily.beridico@gmail.com',
            'email' => 'emily.beridico@cose.org.ph',
            'password' => Hash::make('12312312'),
            'address' => '123 Admin Street',
            'gender' => 'Female',
            'religion' => 'Christian',
            'nationality' => 'Filipino',
            'volunteer_status' => 'Active',
            'status_start_date' => now(),
            'status_end_date' => null,
            'role_id' => 1, // Administrator role
            'status' => 'Active',
            'organization_role_id' => 1, // Executive Director organization role
            'assigned_municipality_id' => null,
            'photo' => null,
            'government_issued_id' => null,
            'sss_id_number' => '1234567890',
            'philhealth_id_number' => '123456789011',
            'pagibig_id_number' => '123456789000',
            'cv_resume' => null,
            'updated_by' => null,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete data from the barangays table
        DB::table('barangays')->where('municipality_id', 1)->delete();
        DB::table('barangays')->where('municipality_id', 2)->delete();

        // Delete data from the municipalities table
        DB::table('municipalities')->where('municipality_name', 'Mondragon')->delete();
        DB::table('municipalities')->where('municipality_name', 'San Roque')->delete();

        // Delete data from the provinces table
        DB::table('provinces')->where('province_name', 'Northern Samar')->delete();

        // Delete data from the beneficiary_categories table
        DB::table('beneficiary_categories')->whereIn('category_name', [
            'Bedridden', 'Bedridden & Living alone', 'Frail', 'Frail/PWD', 
            'Frail/PWD & Living alone', 'Frail/Living alone', 'Frail/Dementia', 'Dementia'
        ])->delete();

        // Delete data from the beneficiary_status table
        DB::table('beneficiary_status')->whereIn('status_name', [
            'Active', 'Inactive', 'Opted Out', 'Deceased', 'Hospitalized', 
            'Moved Residence', 'No Longer Needed Assistance'
        ])->delete();

        // Delete data from the care_categories table
        DB::table('care_categories')->whereIn('care_category_name', [
            'Mobility', 'Cognitive/Communication', 'Self-sustainability', 'Disease/Therapy Handling', 
            'Daily life/Social contact', 'Outdoor Activities', 'Household Keeping'
        ])->delete();

        // Delete data from the roles table
        DB::table('roles')->whereIn('role_name', [
            'admin', 'care_manager', 'care_worker'
        ])->delete();

        // Delete data from the organization_roles table
        DB::table('organization_roles')->whereIn('role_name', [
            'executive_director', 'coordinator'
        ])->delete();

        // Delete data from the history_categories table
        DB::table('history_categories')->whereIn('history_category_name', [
            'Medical Condition', 'Medication', 'Allergy', 'Immunization'
        ])->delete();

        // Delete data from the cose_users table
        DB::table('cose_users')->where('organization_role_id', 1)->delete();
    }
};
