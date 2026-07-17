<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('customer.index', compact('customers'));
    }

    public function create()
    {
        return view('customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email|unique:customers,email',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $customer = Customer::create([
            'customer_name' => $request->customer_name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        ActivityLog::log('Create', 'Menambahkan customer baru: ' . $customer->customer_name);

        return redirect('/customers')
            ->with('success', 'Customer berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email|unique:customers,email,' . $id,
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $customer = Customer::findOrFail($id);

        $customer->update([
            'customer_name' => $request->customer_name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        ActivityLog::log('Update', 'Memperbarui data customer: ' . $customer->customer_name);

        return redirect('/customers')
            ->with('success', 'Customer berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $name = $customer->customer_name;
        $customer->delete();

        ActivityLog::log('Delete', 'Menghapus customer: ' . $name);

        return redirect('/customers')
            ->with('success', 'Customer berhasil dihapus!');
    }
}