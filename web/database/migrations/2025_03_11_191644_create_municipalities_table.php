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
        Schema::create('municipalities', function (Blueprint $table) {
            $table->increments('municipality_id');
            $table->string('municipality_name', 100)->after('municipality_id');
            $table->integer('province_id')->after('municipality_name');
            $table->timestamps();

             // Foreign Key Constraints
             $table->foreign('province_id')->references('province_id')->on('provinces')->onDelete('no action');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};