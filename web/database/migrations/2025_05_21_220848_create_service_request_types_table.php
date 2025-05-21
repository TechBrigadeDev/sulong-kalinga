<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_request_types', function (Blueprint $table) {
            $table->id('service_type_id');
            $table->string('name');
            $table->string('color_code')->default('#0d6efd'); // Default blue color
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default service request types
        DB::table('service_request_types')->insert([
            ['name' => 'Home Care Visit', 'color_code' => '#0d6efd', 'description' => 'Additional home care services', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transportation', 'color_code' => '#198754', 'description' => 'Transportation assistance', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medical Appointments', 'color_code' => '#6610f2', 'description' => 'Assistance with medical appointment visits', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Meal Delivery', 'color_code' => '#fd7e14', 'description' => 'Delivery of prepared meals', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Other Service', 'color_code' => '#6c757d', 'description' => 'Other service requests not categorized above', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_types');
    }
};