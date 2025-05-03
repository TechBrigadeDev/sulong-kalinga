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
        Schema::create('barangays', function (Blueprint $table) {
            $table->increments('barangay_id');
            $table->integer('municipality_id')->after('barangay_id');
            $table->string('barangay_name',100)->after('municipality_id');
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('municipality_id')->references('municipality_id')->on('municipalities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangays');
    }
};
