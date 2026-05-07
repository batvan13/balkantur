<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SuperAdminSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(EntityTypeSeeder::class);
        $this->call(EntitySubtypeSeeder::class);
        $this->call(EntityFeatureSeeder::class);
    }
}
