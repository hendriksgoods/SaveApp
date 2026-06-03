<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\FundUsage;
use App\Models\FundUsageProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FundUsageController extends Controller
{
    public function store(Request $request, Campaign $campaign)
    {
        abort_if($campaign->user_id !== auth()->id(), 403);

        $request->merge([
        'budget' => str_replace('.', '', $request->budget),
        'used'   => str_replace('.', '', $request->used),
        ]);

        $totalBudgetExisting = $campaign->fundUsages()->sum('budget');
        $remaining = $campaign->target_amount - $totalBudgetExisting;  

        $validated = $request->validate([
            'title'      => ['required','string','max:150'],
            'description'=> ['nullable','string'],
            'budget'     => [
                'required','numeric','min:0',
                function($attr,$value,$fail) use ($remaining,$campaign){
                    if($value > $campaign->target_amount){
                        $fail('Anggaran tidak boleh melebihi target campaign ('.
                            'Rp '.number_format($campaign->target_amount,0,',','.').').');
                    }
                    if($value > $remaining && $remaining > 0){
                        $fail('Sisa anggaran yang tersedia hanya Rp '.
                            number_format($remaining,0,',','.').'.');
                    }
                },
            ],
            'used'       => ['required','numeric','min:0'],
            'usage_date' => ['required','date'],
            'status'     => ['required','in:ongoing,done'],
            'proofs'     => ['nullable','array','max:5'],
            'proofs.*'   => ['image','mimes:jpg,jpeg,png,webp','max:3072'],
        ],[
            'budget.required' => 'Anggaran wajib diisi.',
            'used.required'   => 'Dana terpakai wajib diisi.',
        ]);

        $usage = FundUsage::create([
            'campaign_id' => $campaign->id,
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'budget'      => $validated['budget'],
            'used'        => $validated['used'],
            'usage_date'  => $validated['usage_date'],
            'status'      => $validated['status'],
        ]);

        if ($request->hasFile('proofs')) {
            foreach ($request->file('proofs') as $file) {
                $path = $file->store('fund_proofs/'.$campaign->id,'public');
                FundUsageProof::create(['fund_usage_id'=>$usage->id,'image_path'=>$path]);
            }
        }

        return back()->with('success','Laporan penggunaan dana berhasil ditambahkan.');
    }

    public function destroy(Campaign $campaign, FundUsage $usage)
    {
        abort_if($campaign->user_id !== auth()->id(), 403);
        foreach ($usage->proofs as $proof) {
            Storage::disk('public')->delete($proof->image_path);
            $proof->delete();
        }
        $usage->delete();
        return back()->with('success','Laporan berhasil dihapus.');
    }
}
