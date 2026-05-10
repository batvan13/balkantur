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
        Schema::create('food_place_features', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('feature_group', 32);
            $table->timestamps();
        });

        Schema::create('entity_food_place_feature', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('food_place_feature_id')->constrained('food_place_features')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['entity_id', 'food_place_feature_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_food_place_feature');
        Schema::dropIfExists('food_place_features');
    }
};
