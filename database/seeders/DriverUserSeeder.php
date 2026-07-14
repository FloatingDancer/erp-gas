<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\User;

class DriverUserSeeder extends Seeder
{
    public function run()
    {
        $driverEmail = config('app.demo') ? 'driverguest@gmail.com' : 'driver@gmail.com';

        User::where('email', 'driver@gmail.com')->delete();
        User::where('email', 'driverguest@gmail.com')->delete();

        $driver = Driver::create([
            'name' => 'Budi Santoso (Guest)',
            'phone' => '08123456789',
            'license_plate' => 'B 4321 NSJ',
            'status' => 'Active'
        ]);

        User::create([
            'name' => 'Budi Santoso (Guest)',
            'email' => $driverEmail,
            'password' => bcrypt('12345678'),
            'role' => 'driver',
            'driver_id' => $driver->id
        ]);
    }
}
