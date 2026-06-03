<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\ForumComment;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    // Donatur kirim komentar utama
    public function store(Request $request, Campaign $campaign)
    {
        abort_if(!auth()->check(), 403, 'Login dulu untuk berkomentar.');

        $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ], [
            'body.required' => 'Komentar tidak boleh kosong.',
            'body.min'      => 'Komentar minimal 3 karakter.',
            'body.max'      => 'Komentar maksimal 1000 karakter.',
        ]);

        ForumComment::create([
            'campaign_id' => $campaign->id,
            'user_id'     => auth()->id(),
            'parent_id'   => null,
            'body'        => $request->body,
        ]);

        return back()
            ->with('success', 'Komentar berhasil dikirim.')
            ->withFragment('forum');
    }

    // Penggalang balas komentar donatur
    public function reply(Request $request, Campaign $campaign, ForumComment $comment)
    {
        // Hanya pemilik campaign (penggalang) yang bisa balas
        abort_if($campaign->user_id !== auth()->id(), 403, 'Hanya penggalang dana yang bisa membalas.');
        abort_if($comment->campaign_id !== $campaign->id, 403);

        $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        ForumComment::create([
            'campaign_id' => $campaign->id,
            'user_id'     => auth()->id(),
            'parent_id'   => $comment->id,
            'body'        => $request->body,
        ]);

        return back()
            ->with('success', 'Balasan berhasil dikirim.')
            ->withFragment('forum');
    }


    // Hapus komentar — penggalang pemilik campaign bisa hapus semua komentar
    public function destroy(Campaign $campaign, ForumComment $comment)
    {
        abort_if($campaign->user_id !== auth()->id(), 403);
        abort_if($comment->campaign_id !== $campaign->id, 403);

        // Hapus juga semua reply-nya
        $comment->replies()->delete();
        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.')->withFragment('forum');
    }

    // Hapus reply
    public function destroyReply(Campaign $campaign, ForumComment $comment)
    {
        abort_if($campaign->user_id !== auth()->id(), 403);
        abort_if($comment->campaign_id !== $campaign->id, 403);

        $comment->delete();

        return back()->with('success', 'Balasan berhasil dihapus.')->withFragment('forum');
    }

    // Like komentar
    public function like(Campaign $campaign, ForumComment $comment)
    {
        abort_if(!auth()->check(), 403);
        $comment->increment('likes');
        return back()->withFragment('forum');
    }
}
