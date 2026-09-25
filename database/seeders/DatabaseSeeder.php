<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Wajib dipanggil agar bisa membuat akun

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Akun Super Admin Utama (Kunci Master)
        User::updateOrCreate(
            ['email' => 'admin@kimpaidynatube.com'],
            [
                'name' => 'Super Admin Utama',
                'password' => Hash::make('12345678'),
                'role' => 'admin' // Role tertinggi yang bisa mengakses semua menu
            ]
        );

        // 2. Panggil seeder Master Item
        $this->call([
            MasterItemSeeder::class,
        ]);
    }
}