<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::all();
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'license_plate' => 'required|string|max:20',
            'status' => 'required|in:Active,Inactive',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:6',
        ]);

        $driver = Driver::create($request->only(['name', 'phone', 'license_plate', 'status']));

        if ($request->filled('email') && $request->filled('password')) {
            \App\Models\User::create([
                'name' => $driver->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'driver',
                'driver_id' => $driver->id,
            ]);
        }

        ActivityLog::log('Create', 'Menambahkan driver baru: ' . $driver->name . ' (' . $driver->license_plate . ')');

        return redirect()->route('drivers.index')
            ->with('success', 'Driver berhasil ditambahkan!');
    }

    public function edit(Driver $driver)
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $user = \App\Models\User::where('driver_id', $driver->id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'license_plate' => 'required|string|max:20',
            'status' => 'required|in:Active,Inactive',
            'email' => 'nullable|email|unique:users,email' . ($user ? ',' . $user->id : ''),
            'password' => 'nullable|string|min:6',
        ]);

        $driver->update($request->only(['name', 'phone', 'license_plate', 'status']));

        if ($user) {
            $userData = [
                'name' => $driver->name,
            ];
            if ($request->filled('email')) {
                $userData['email'] = $request->email;
            }
            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }
            $user->update($userData);
        } else {
            if ($request->filled('email') && $request->filled('password')) {
                \App\Models\User::create([
                    'name' => $driver->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'role' => 'driver',
                    'driver_id' => $driver->id,
                ]);
            }
        }

        ActivityLog::log('Update', 'Memperbarui data driver: ' . $driver->name);

        return redirect()->route('drivers.index')
            ->with('success', 'Driver berhasil diperbarui!');
    }

    public function destroy(Driver $driver)
    {
        $name = $driver->name;
        
        // Hapus akun user driver terkait jika ada
        \App\Models\User::where('driver_id', $driver->id)->delete();
        
        $driver->delete();

        ActivityLog::log('Delete', 'Menghapus driver: ' . $name);

        return redirect()->route('drivers.index')
            ->with('success', 'Driver berhasil dihapus!');
    }
}
