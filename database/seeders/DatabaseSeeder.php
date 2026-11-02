<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bencana.com'],
            [
                'nama' => 'Administrator BPBD',
                'password' => Hash::make('admin123'),
                'role' => 'ADMIN',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama' => 'Admin Utama',
                'password' => Hash::make('admin123'),
                'role' => 'ADMIN',
                'is_active' => true,
            ]
        );

        $this->call([
            KabupatenBogorWilayahSeeder::class,
            DisasterDataSeeder::class,
            DemoDisasterReportSeeder::class,
            RegionalAndSupportSeeder::class,
        ]);
    }
}