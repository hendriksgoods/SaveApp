<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    private array $categories = [
        'Pendidikan',
        'Kesehatan',
        'Bencana Alam',
        'Alam & Lingkungan',
        'Kebutuhan Sehari-hari',
    ];

    public function index(Request $request)
    {
        $search   = $request->get('search');
        $category = $request->get('category', 'Semua');
        $sort     = $request->get('sort', 'newest');

        $query = Campaign::with('user')->active()->byCategory($category)->search($search);

        match ($sort) {
            'most_raised' => $query->orderByDesc('raised_amount'),
            'ending_soon' => $query->orderBy('deadline'),
            default       => $query->orderByDesc('created_at'),
        };

        $campaigns  = $query->paginate(9)->withQueryString();
        $categories = array_merge(['Semua'], $this->categories);

        return view('campaigns.index', compact('campaigns', 'categories', 'search', 'category', 'sort'));
    }

    public function show(Campaign $campaign)
    {
        abort_if($campaign->status !== 'active', 404);

        $campaign->load([
            'user',
            'galleries',
            'fundUsages.proofs',
            'withdrawals',
            'updates' => fn($q) => $q->latest(),
            'donations' => fn($q) => $q->where('payment_status', 'paid')->latest()->take(10),
        ]);

        // Forum: top-level comments with user + replies with user
        $comments = $campaign->comments()
            ->topLevel()
            ->with(['user', 'replies.user'])
            ->withCount('replies')
            ->latest()
            ->get();

        // Laporan summary
        $totalBudget = $campaign->fundUsages->sum('budget');
        $totalUsed   = $campaign->fundUsages->sum('used');
        $totalSisa   = max(0, $totalBudget - $totalUsed);

        $tab = request()->get('tab', 'informasi');

        return view('campaigns.show', compact(
            'campaign', 'comments',
            'totalBudget', 'totalUsed', 'totalSisa',
            'tab'
        ));
    }

    public function create()
    {
        abort_if(!auth()->check() || !auth()->user()->isPenggalang(), 403);
  
        // Penggalang harus sudah diverifikasi admin
        if (!auth()->user()->is_verified) {
            return redirect()->route('verification.request')
                ->with('info', 'Akun Anda belum diverifikasi. Ajukan verifikasi terlebih dahulu.');
        }
        return view('campaigns.create', ['categories' => $this->categories]);  
    }

    public function store(Request $request)
    {
        abort_if(!auth()->check() || !auth()->user()->isPenggalang(), 403);

        if (!auth()->user()->is_verified) {
            return redirect()->route('verification.request')
                ->with('info', 'Akun Anda belum diverifikasi.');
        }


        $validated = $request->validate([
            'category'        => ['required', 'in:' . implode(',', $this->categories)],
            'full_name_ktp'   => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/', 'min:3'],
            'ktp_number'      => ['required', 'digits:16'],
            'phone'           => ['required', 'digits:12'],
            'occupation'      => ['required', 'string', 'max:100'],
            'facebook'        => ['nullable', 'url'],
            'instagram'       => ['nullable', 'url'],
            'twitter'         => ['nullable', 'url'],
            'title'           => ['required', 'string', 'min:10', 'max:100'],
            'story'           => ['required', 'string', 'min:50'],
            'description'     => ['required', 'string', 'min:200'],
            'fund_purpose'    => ['required', 'string'],
            'location'        => ['required', 'string'],
            'target_amount'   => ['required', 'numeric', 'min:100000'],
            'duration_days'   => ['required', 'integer', 'min:7', 'max:365'],
            'fund_detail'     => ['required', 'string', 'min:100'],
            'reason'          => ['required', 'string', 'min:300'],
            'image'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('campaigns', 'public');
        }

        Campaign::create(array_merge($validated, [
            'user_id'       => auth()->id(),
            'slug'          => Campaign::generateSlug($validated['title']),
            'raised_amount' => 0,
            'deadline'      => now()->addDays((int) $validated['duration_days']),
            'image'         => $imagePath,
            'status'        => 'pending',
        ]));

        return redirect()->route('fundraiser.dashboard')
            ->with('success', 'Campaign berhasil dikirim! Menunggu review admin (1–3 hari kerja).');
    }

    public function edit(Campaign $campaign)
    {
        abort_if($campaign->user_id !== auth()->id(), 403);
        abort_if($campaign->status !== 'rejected', 403);
        return view('campaigns.create', [
            'campaign'   => $campaign,
            'categories' => $this->categories,
            'editing'    => true,
        ]);
    }

    public function update(Request $request, Campaign $campaign)
    {
        abort_if($campaign->user_id !== auth()->id(), 403);
        abort_if($campaign->status !== 'rejected', 403);

        $validated = $request->validate([
            'category'      => ['required', 'in:' . implode(',', $this->categories)],
            'full_name_ktp' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/', 'min:3'],
            'ktp_number'    => ['required', 'digits:16'],
            'phone'         => ['required', 'digits:12'],
            'occupation'    => ['required', 'string'],
            'facebook'      => ['nullable', 'url'],
            'instagram'     => ['nullable', 'url'],
            'twitter'       => ['nullable', 'url'],
            'title'         => ['required', 'string', 'min:10'],
            'story'         => ['required', 'string', 'min:50'],
            'description'   => ['required', 'string', 'min:200'],
            'fund_purpose'  => ['required', 'string'],
            'location'      => ['required', 'string'],
            'target_amount' => ['required', 'numeric', 'min:100000'],
            'duration_days' => ['required', 'integer', 'min:7', 'max:365'],
            'fund_detail'   => ['required', 'string', 'min:100'],
            'reason'        => ['required', 'string', 'min:300'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($campaign->image) Storage::disk('public')->delete($campaign->image);
            $validated['image'] = $request->file('image')->store('campaigns', 'public');
        }

        $campaign->update(array_merge($validated, [
            'status'           => 'pending',
            'rejection_reason' => null,
            'deadline'         => now()->addDays((int) $validated['duration_days']),
        ]));

        return redirect()->route('fundraiser.dashboard')
            ->with('success', 'Campaign diperbarui dan dikirim ulang untuk review.');
    }
}
