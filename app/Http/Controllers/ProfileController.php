<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman edit profile
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update profile user
     */
    public function update(Request $request): RedirectResponse
    {
        if ($request->user()->email === 'guest@gmail.com') {
            return redirect()->back()->withErrors(['error' => 'Mengubah profil tidak diperbolehkan pada akun demo.'], 'updateProfileInformation');
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $user = $request->user();

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return redirect('/my-profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Hapus akun
     */
    public function destroy(Request $request): RedirectResponse
    {
        if ($request->user()->email === 'guest@gmail.com') {
            abort(403, 'Aksi ini tidak diperbolehkan pada akun demo.');
        }

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}