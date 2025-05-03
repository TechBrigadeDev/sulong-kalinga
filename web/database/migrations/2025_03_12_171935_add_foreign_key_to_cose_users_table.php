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
        Schema::table('cose_users', function (Blueprint $table) {
            $table->foreign('assigned_municipality_id')->references('municipality_id')->on('municipalities')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cose_users', function (Blueprint $table) {
            $table->dropForeign(['assigned_municipality_id']);
        });
    }
};
