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
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('building_no')->unique();
            $table->enum('type', ['new', 'old'])->default('new');
            $table->string('location');
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
            $table->enum('status', ['planned', 'in_progress', 'completed', 'on_hold'])->default('planned');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
