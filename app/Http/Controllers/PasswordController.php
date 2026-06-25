<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    // Halaman form forgot password
    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    // Proses kirim token reset
    public function sendReset(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ],[
            'email.exists' => 'Email tidak terdaftar.',
        ]);

        // Hapus token lama kalau ada
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Buat token baru
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        // Simpan token di session untuk simulasi (tanpa email server)
        // Di production, kirim via email
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);

        return back()->with([
            'success'   => 'Link reset password berhasil dibuat.',
            'reset_url' => $resetUrl, // tampil langsung karena tidak ada email server
        ]);
    }

    // Halaman form reset password
    public function showReset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
        ]);

        return view('auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email,
        ]);
    }

    // Proses reset password
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ],[
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Cari token
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Link reset tidak valid.']);
        }

        // Cek token expired (1 jam)
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->diffInMinutes(now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Link reset sudah kadaluarsa. Minta link baru.']);
        }

        // Verifikasi token
        if (!Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Link reset tidak valid.']);
        }

        // Update password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Hapus token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
    // Halaman ganti password (untuk user yang sudah login)
    // public function showChange()
    // {
    //     return view('auth.change-password');
    // }

    // Proses ganti password
//     public function change(Request $request)
//     {
//         $request->validate([
//             'current_password' => ['required'],
//             'password'         => ['required', 'min:8', 'confirmed'],
//         ],[
//             'password.min'       => 'Password baru minimal 8 karakter.',
//             'password.confirmed' => 'Konfirmasi password tidak cocok.',
//         ]);

//         // Cek password lama
//         if (!Hash::check($request->current_password, auth()->user()->password)) {
//             return back()->withErrors(['current_password' => 'Password saat ini tidak benar.']);
//         }

//         auth()->user()->update([
//             'password' => Hash::make($request->password),
//         ]);

//         return back()->with('success', 'Password berhasil diubah.');
//     }
// }
