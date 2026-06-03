<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->get('search');
        $category = $request->get('category', 'Semua');

        $campaigns = Campaign::with('user')
            ->active()
            ->byCategory($category)
            ->search($search)
            ->orderByDesc('is_urgent')
            ->orderByDesc('created_at')
            ->paginate(6)
            ->withQueryString();

        $stats = [
            'total_raised'    => Campaign::active()->sum('raised_amount'),
            'total_donors'    => Donation::where('payment_status', 'paid')->distinct('donor_email')->count(),
            'total_campaigns' => Campaign::active()->count(),
        ];

        $categories = ['Semua', 'Kesehatan', 'Pendidikan', 'Bencana Alam', 'Alam & Lingkungan', 'Kebutuhan Sehari-hari'];

        return view('home.index', compact('campaigns', 'stats', 'categories', 'search', 'category'));
    }

    // Penggalang Dana dashboard
    public function dashboard()
    {
        abort_if(!auth()->user()->isPenggalang(), 403);
        $campaigns   = Campaign::where('user_id', auth()->id())->latest()->get();
        $totalRaised = $campaigns->sum('raised_amount');
        return view('fundraiser.dashboard', compact('campaigns', 'totalRaised'));
    }
}
