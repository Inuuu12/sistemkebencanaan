<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KabupatenBogorWilayahSeeder::class,
            DisasterDataSeeder::class,
            DemoDisasterReportSeeder::class,
            RegionalAndSupportSeeder::class,
        ]);
    }
}
