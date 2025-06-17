<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftTracksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shift_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->foreignId('care_worker_id')->constrained('cose_users')->onDelete('cascade');
            // $table->decimal('latitude', 10, 7);
            // $table->decimal('longitude', 10, 7);
            $table->json('track_coordinates')->nullable();
            $table->string('address')->nullable();
            $table->timestamp('recorded_at');
            $table->boolean('synced')->default(true);
            $table->timestamps();
            $table->unsignedBigInteger('visitation_id')->nullable()->after('care_worker_id');
            $table->enum('arrival_status', ['arrived', 'departed'])->nullable()->after('address');
            $table->foreign('visitation_id')->references('id')->on('visitations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_tracks');
    }
};
