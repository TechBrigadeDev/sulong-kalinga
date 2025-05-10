<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('visitation_exceptions', function (Blueprint $table) {
            $table->id('exception_id');
            $table->unsignedBigInteger('visitation_id');
            $table->date('exception_date');
            $table->string('status'); // 'canceled' or other possible statuses
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('visitation_id')->references('visitation_id')->on('visitations')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('cose_users');
            
            // Ensure each date can only have one exception per visitation
            $table->unique(['visitation_id', 'exception_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitation_exceptions');
    }
};
