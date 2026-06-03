<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'email' => ['required','email','unique:users,email,'.$user->id],
            'phone' => ['required','digits:12'],
        ],[
            'email.unique'  => 'Email sudah digunakan akun lain.',
            'phone.digits'  => 'Nomor telepon harus tepat 12 angka.',
        ]);

        $user->update([
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        return back()->with('success','Profil berhasil diperbarui.');
    }
}
