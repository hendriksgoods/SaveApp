<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    // Halaman form pengajuan verifikasi (penggalang)
    public function request()
    {
        abort_if(!auth()->user()->isPenggalang(), 403);

        if (auth()->user()->is_verified) {
            return redirect()->route('campaigns.create');
        }

        return view('verification.request');
    }

    // Penggalang submit form verifikasi
    public function submit(Request $request)
    {
        abort_if(!auth()->user()->isPenggalang(), 403);

        $request->validate([
            'verify_full_name'   => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/', 'min:3'],
            'verify_ktp_number'  => ['required', 'digits:16'],
        ],[
            'verify_full_name.required' => 'Nama lengkap wajib diisi.',
            'verify_full_name.regex'    => 'Nama lengkap hanya boleh huruf dan spasi, tidak boleh angka.',
            'verify_full_name.min'      => 'Nama lengkap minimal 3 karakter.',
            'verify_ktp_number.required'=> 'Nomor KTP wajib diisi.',
            'verify_ktp_number.digits'  => 'Nomor KTP harus tepat 16 angka.',
        ]);

        auth()->user()->update([
            'verify_full_name'    => $request->verify_full_name,
            'verify_ktp_number'   => $request->verify_ktp_number,
            'verification_status' => 'pending',
            'rejection_reason'    => null,
        ]);

        return back()->with('success', 'Pengajuan verifikasi berhasil dikirim! Admin akan mereview dalam 1-2 hari kerja.');
    }

    // Admin: list semua penggalang
    public function index()
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $pending  = User::where('role','penggalang')
                        ->where('verification_status','pending')
                        ->latest()->get();

        $verified = User::where('role','penggalang')
                        ->where('verification_status','approved')
                        ->latest()->get();

        $rejected = User::where('role','penggalang')
                        ->where('verification_status','rejected')
                        ->latest()->get();

        $none     = User::where('role','penggalang')
                        ->where('verification_status','none')
                        ->latest()->get();

        return view('admin.verifications', compact('pending','verified','rejected','none'));
    }

    // Admin: approve
    public function approve(User $user)
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        abort_if(!$user->isPenggalang(), 400);

        $user->update([
            'is_verified'         => true,
            'verified_at'         => now(),
            'verification_status' => 'approved',
            'rejection_reason'    => null,
        ]);

        return back()->with('success', 'Akun '.$user->name.' berhasil diverifikasi.');
    }

    // Admin: reject dengan alasan
    public function reject(Request $request, User $user)
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $request->validate([
            'rejection_reason' => ['required','string','min:10'],
        ],['rejection_reason.required' => 'Alasan penolakan wajib diisi.']);

        $user->update([
            'is_verified'         => false,
            'verified_at'         => null,
            'verification_status' => 'rejected',
            'rejection_reason'    => $request->rejection_reason,
        ]);

        return back()->with('success', 'Pengajuan verifikasi '.$user->name.' ditolak.');
    }

    // Admin: revoke yang sudah approved
    public function revoke(Request $request, User $user)
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $request->validate([
            'rejection_reason' => ['required','string','min:5'],
        ],['rejection_reason.required' => 'Alasan pencabutan wajib diisi.']);

        $user->update([
            'is_verified'         => false,
            'verified_at'         => null,
            'verification_status' => 'rejected',
            'rejection_reason'    => $request->rejection_reason,
        ]);

        return back()->with('success', 'Verifikasi akun '.$user->name.' dicabut.');
    }
}
