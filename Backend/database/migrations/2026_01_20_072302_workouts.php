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
        Schema::create('workouts', function(Blueprint $table) {
            $table->id();

            $table->foreignId('activity_id')
                  ->constrained('activities')
                  ->onDelete('cascade');
            $table->date('date');

            $table->unsignedTinyInteger('effort_lever');

            $table->decimal('distance_value', 8, 2)->nullable();
            $table->string('distance_unit',10)->nullable();

            $table->integer('duration_value')->nullable();
            $table->string('duration_unit',10)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
