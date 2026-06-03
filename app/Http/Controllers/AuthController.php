<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'min:3', 'regex:/^[a-zA-Z\s]+$/'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['required', 'digits:12'],
            'password' => ['required', 'min:8'],
            'role'     => ['required', 'in:donatur,penggalang'],
        ], [
            'name.regex'      => 'Nama hanya boleh huruf dan spasi, tidak boleh mengandung angka.',
            'name.min'        => 'Nama minimal 3 karakter.',
            'email.unique'    => 'Email sudah terdaftar.',
            'phone.digits'    => 'Nomor telepon harus tepat 12 angka.',
            'password.min'    => 'Password minimal 8 karakter.',
            'role.in'         => 'Pilih tipe akun yang valid.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Selamat datang, ' . $user->name . '!');
    }

    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isPenggalang()) {
                return redirect()->route('home')
                    ->with('success', 'Selamat datang, ' . $user->name . '!');
            }
            return redirect()->route('home')
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
