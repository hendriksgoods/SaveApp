<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    // Upload foto galeri — hanya penggalang pemilik campaign
    public function store(Request $request, Campaign $campaign)
    {
        abort_if($campaign->user_id !== auth()->id(), 403);

        $request->validate([
            'images'   => ['required', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'images.required'  => 'Pilih minimal 1 foto.',
            'images.*.image'   => 'File harus berupa gambar.',
            'images.*.max'     => 'Ukuran foto maksimal 3MB.',
        ]);

        foreach ($request->file('images') as $file) {
            $path = $file->store('galleries/' . $campaign->id, 'public');
            CampaignGallery::create([
                'campaign_id' => $campaign->id,
                'image_path'  => $path,
            ]);
        }

        return back()->with('success', 'Foto berhasil diunggah ke galeri.');
    }

    // Hapus foto galeri
    public function destroy(Campaign $campaign, CampaignGallery $gallery)
    {
        abort_if($campaign->user_id !== auth()->id(), 403);
        abort_if($gallery->campaign_id !== $campaign->id, 403);

        Storage::disk('public')->delete($gallery->image_path);
        $gallery->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
