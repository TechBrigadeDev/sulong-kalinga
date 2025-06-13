<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('language_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('user_type'); // 'cose_user', 'beneficiary', 'family_member'
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            // Composite unique key to prevent duplicates
            $table->unique(['user_type', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('language_preferences');
    }
};