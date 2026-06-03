<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($req,$next){
            if(!auth()->check()||!auth()->user()->isAdmin()) abort(403);
            return $next($req);
        });
    }

    public function dashboard()
    {
        $pending  = Campaign::with('user')->pending()->latest()->get();
        $allCount = Campaign::pending()->count();
        return view('admin.dashboard', compact('pending','allCount'));
    }

    public function review(Campaign $campaign)
    {
        abort_if($campaign->status!=='pending',404);
        $campaign->load('user');
        return view('admin.review', compact('campaign'));
    }

    public function approve(Campaign $campaign)
    {
        abort_if($campaign->status!=='pending',400);
        $campaign->update([
            'status'           => 'active',
            'rejection_reason' => null,
            'deadline'         => now()->addDays($campaign->duration_days),
        ]);
        return redirect()->route('admin.dashboard')
            ->with('success','Campaign "'.$campaign->title.'" berhasil disetujui.');
    }

    public function reject(Request $request, Campaign $campaign)
    {
        $request->validate([
            'rejection_reason' => ['required','string','min:10'],
        ],['rejection_reason.required'=>'Alasan penolakan wajib diisi.']);

        $campaign->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success','Campaign "'.$campaign->title.'" ditolak.');
    }
}
