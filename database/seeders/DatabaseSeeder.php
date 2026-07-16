<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Client',
                'password' => bcrypt('12345678'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'guest@gmail.com'],
            [
                'name' => 'Guest Evaluator',
                'password' => bcrypt('12345678'),
                'role' => 'admin',
            ]
        );

        \App\Models\Supplier::updateOrCreate(
            ['name' => 'PT. Pertamina Gas Retail'],
            [
                'phone' => '021-500000',
                'email' => 'sales@pertaminagas.co.id',
                'address' => 'Jl. Perwira No. 2-4, Jakarta Pusat',
            ]
        );

        \App\Models\Supplier::updateOrCreate(
            ['name' => 'Agen Elpiji Cikarang Abadi'],
            [
                'phone' => '08123456789',
                'email' => 'cikarang_abadi@gmail.com',
                'address' => 'Kawasan Industri Jababeka, Cikarang, Bekasi',
            ]
        );

        $this->call([
            DriverUserSeeder::class,
        ]);
    }
}
