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
        Schema::table('places', function (Blueprint $table) {
            $table->string('ekatte_code')->unique()->after('type');
            $table->string('municipality_name')->nullable()->after('ekatte_code');
            $table->string('region_name')->nullable()->after('municipality_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn(['ekatte_code', 'municipality_name', 'region_name']);
        });
    }
};
