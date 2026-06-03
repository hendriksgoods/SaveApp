<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    public function store(Request $request, Campaign $campaign)
    {
        abort_if($campaign->status !== 'active', 400);

        // Hanya donatur yang bisa donasi
        if (auth()->check() && !auth()->user()->isDonatur()) {
            return back()->with('error','Hanya akun Donatur yang dapat melakukan donasi.');
        }
        
        $request->merge([
            'amount' => str_replace('.', '', $request->amount),
        ]);

        $validated = $request->validate([
            'donor_name'   => ['required','string','max:100'],
            'donor_email'  => ['required','email'],
            'amount'       => ['required','numeric','min:10000'],
            'message'      => ['nullable','string','max:500'],
            'is_anonymous' => ['boolean'],
        ],[
            'amount.min' => 'Donasi minimal Rp 10.000.',
        ]);

        $donation = Donation::create([
            'campaign_id'    => $campaign->id,
            'user_id'        => auth()->id(),
            'donor_name'     => $validated['donor_name'],
            'donor_email'    => $validated['donor_email'],
            'amount'         => $validated['amount'],
            'message'        => $validated['message'] ?? null,
            'is_anonymous'   => $request->boolean('is_anonymous'),
            'payment_status' => 'paid',
            'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
        ]);

        $campaign->increment('raised_amount', $validated['amount']);


        return view('payment.processing', [
    'donation'   => $donation,
    'campaign'   => $campaign,
    'paymentUrl' => 'https://app.sandbox.midtrans.com/payment-links/b9815576-de52-44a6-a858-e7e25fd55b5e',
    ]);
  }   
}
