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
        Schema::create('organization_roles', function (Blueprint $table) {
            $table->increments('organization_role_id');
            $table->string('role_name',50)->after('organization_role_id');
            $table->string('area',100)->nullable()->after('role_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_roles');
    }
};
