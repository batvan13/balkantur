<?php

use App\Models\Entity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entity_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->string('locale', 16);
            $table->string('name');
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['entity_id', 'locale']);
        });

        $now = now();

        Entity::query()->orderBy('id')->chunkById(100, function ($entities) use ($now): void {
            foreach ($entities as $entity) {
                DB::table('entity_translations')->insert([
                    'entity_id' => $entity->id,
                    'locale' => 'bg',
                    'name' => $entity->name,
                    'address' => $entity->address,
                    'description' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_translations');
    }
};
