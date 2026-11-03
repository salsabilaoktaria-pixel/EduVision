<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{
    public function gantiPassword()
    {
        return view('akun.ganti-password');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8|confirmed|different:password_lama',
        ], [
            'password_baru.different' => 'Password baru harus berbeda dari password lama.',
        ]);

        if (!Hash::check($validated['password_lama'], $user->password)) {
            return back()->withInput()->withErrors(['password_lama' => 'Password lama yang kamu masukkan salah.']);
        }

        $user->password = Hash::make($validated['password_baru']);
        $user->save();

        return back()->with('sukses', 'Password berhasil diganti. Gunakan password baru ini untuk login berikutnya.');
    }
}
