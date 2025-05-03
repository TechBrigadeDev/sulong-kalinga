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
            $table->foreign('organization_role_id')->references('organization_role_id')->on('organization_roles')->onDelete('no action');
            $table->foreign('updated_by')->references('id')->on('cose_users')->onDelete('no action');        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cose_users', function (Blueprint $table) {
            $table->dropForeign(['organization_role_id']);
            $table->dropForeign(['assigned_municipality_id']);
            $table->dropForeign(['updated_by']);
        });
    }
};
