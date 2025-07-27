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
        Schema::create('building_progress_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_actual_progress_id')->constrained('building_actual_progress')->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('caption')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_progress_photos');
    }
};
