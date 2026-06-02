<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\User;

class DriverUserSeeder extends Seeder
{
    public function run()
    {
        User::where('email', 'driver@example.com')->delete();

        $driver = Driver::create([
            'name' => 'Budi Santoso',
            'phone' => '08123456789',
            'license_plate' => 'B 4321 NSJ',
            'status' => 'Active'
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'driver@example.com',
            'password' => bcrypt('password'),
            'role' => 'driver',
            'driver_id' => $driver->id
        ]);
    }
}
