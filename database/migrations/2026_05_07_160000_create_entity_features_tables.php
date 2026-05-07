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
        Schema::create('entity_features', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('entity_type_id')->constrained('entity_types')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('feature_group', 32);
            $table->timestamps();
        });

        Schema::create('entity_entity_feature', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('entity_feature_id')->constrained('entity_features')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['entity_id', 'entity_feature_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_entity_feature');
        Schema::dropIfExists('entity_features');
    }
};
