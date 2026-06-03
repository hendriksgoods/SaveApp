<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignUpdateController extends Controller
{
    // Penggalang tambah Jejak Kebaikan
    public function store(Request $request, Campaign $campaign)
    {
        abort_if($campaign->user_id !== auth()->id(), 403);

        $request->validate([
            'title'       => ['required','string','max:150'],
            'description' => ['required','string','min:5'],
            'image_path'  => ['nullable','image','mimes:jpg,jpeg,png,webp','max:3072'],
        ],[
            'title.required'       => 'Judul wajib diisi.',
            'description.required' => 'Deskripsi wajib diisi.',
            'description.min'      => 'Deskripsi minimal 10 karakter.',
        ]);

        $imgPath = null;
        if ($request->hasFile('image_path')) {
            $imgPath = $request->file('image_path')
                ->store('updates/'.$campaign->id, 'public');
        }

        CampaignUpdate::create([
            'campaign_id' => $campaign->id,
            'title'       => $request->title,
            'description' => $request->description,
            'image_path'  => $imgPath,
            'update_date' => $request->update_date,
        ]);

        return redirect()->route('campaigns.show', [
                                 'campaign' => $campaign->slug,
                                 'tab' => 'jejak'
        ])->with('success', 'Jejak Kebaikan berhasil ditambahkan.');
    }

    public function destroy(Campaign $campaign, CampaignUpdate $update)
    {
        abort_if($campaign->user_id !== auth()->id(), 403);
        if ($update->image_path) Storage::disk('public')->delete($update->image_path);
        $update->delete();
        return back()->with('success','Update berhasil dihapus.');
    }
}
