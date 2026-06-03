<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    // List semua penarikan milik penggalang
    public function index(Campaign $campaign)
    {
        abort_if($campaign->user_id !== auth()->id(), 403);
        $withdrawals = Withdrawal::where('campaign_id', $campaign->id)->latest()->get();
        return view('withdrawals.index', compact('campaign', 'withdrawals'));
    }

    // Form + simpan penarikan baru
    public function store(Request $request, Campaign $campaign)
    {
        abort_if($campaign->user_id !== auth()->id(), 403);
        // Saldo yang bisa ditarik = raised - sudah ditarik
        $totalWithdrawn = Withdrawal::where('campaign_id', $campaign->id)
            ->where('status', 'processed')
            ->sum('amount');
        $available = $campaign->raised_amount - $totalWithdrawn;

        $validated = $request->validate([
            'amount'         => ['required', 'numeric', 'min:10000', "max:$available"],
            'bank_name'      => ['required', 'string'],
            'account_number' => ['required', 'string','regex:/^[0-9]{8,16}$/'],
            'account_name' => ['required','regex:/^[a-zA-Z\s]+$/'],
            'description'    => ['required', 'string','min:10','max:500'],
            // 'account_name'   => ['required', 'string'],
            // 'account_number' => ['required','min: 8','digits:16'],
            
        ], [
            'amount.min'         => 'Penarikan minimal Rp 10.000.',
            'amount.max'         => 'Jumlah melebihi saldo tersedia (Rp ' . number_format($available, 0, ',', '.') . ').',
            'description.required' => 'Keterangan penarikan wajib diisi.',
        ]);

        Withdrawal::create([
            'campaign_id'    => $campaign->id,
            'user_id'        => auth()->id(),
            'amount'         => $validated['amount'],
            'bank_name'      => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name'   => $validated['account_name'],
            'description'    => $validated['description'] ?? null,
            'status'         => 'processed',
            'processed_at'   => now(),
        ]);

        return back()->with('success', 'Penarikan berhasil!');
    }
}
