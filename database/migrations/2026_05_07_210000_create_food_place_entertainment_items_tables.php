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
        Schema::create('food_place_entertainment_items', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('metadata_group', 32);
            $table->timestamps();
        });

        Schema::create('entity_food_place_entertainment_item', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->unsignedBigInteger('food_place_entertainment_item_id');
            $table->timestamps();

            $table->unique(['entity_id', 'food_place_entertainment_item_id'], 'efpei_entity_item_uq');
            $table->foreign('food_place_entertainment_item_id', 'efpei_item_fk')
                ->references('id')->on('food_place_entertainment_items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_food_place_entertainment_item');
        Schema::dropIfExists('food_place_entertainment_items');
    }
};
