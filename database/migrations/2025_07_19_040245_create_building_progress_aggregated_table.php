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
        Schema::create('building_progress_aggregated', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->date('progress_date');

            // Scheduled progress aggregates
            $table->decimal('scheduled_total_weightage', 10, 2)->default(0);
            $table->decimal('scheduled_completed_weightage', 10, 2)->default(0);
            $table->decimal('scheduled_percentage', 5, 2)->default(0);

            // Actual progress aggregates
            $table->decimal('actual_total_weightage', 10, 2)->default(0);
            $table->decimal('actual_completed_weightage', 10, 2)->default(0);
            $table->decimal('actual_percentage', 5, 2)->default(0);

            // Variance
            $table->decimal('variance', 5, 2)->default(0);

            $table->timestamps();

            $table->unique(['building_id', 'progress_date']);
            $table->index('progress_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_progress_aggregated');
    }
};
