<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Faq;
use App\Models\Setting;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function faqs()
    {
        return Faq::where('is_active', true)->orderBy('sort')->get();
    }

    public function about()
    {
        return response()->json(Setting::get('about'));
    }

    public function events()
    {
        return Announcement::live()->whereIn('kind', ['event', 'notice'])->latest()->get();
    }

    public function app()
    {
        return response()->json(['download_url' => Setting::get('app_download_url')]);
    }

    public function supportContact()
    {
        return response()->json(Setting::get('support_contact'));
    }

    public function tickets(Request $r)
    {
        return SupportTicket::where('user_id', $r->user()->id)->latest()->paginate(20);
    }

    public function createTicket(Request $r)
    {
        $d = $r->validate(['subject' => 'required|string|max:150', 'body' => 'required|string|max:4000']);
        $t = SupportTicket::create(['user_id' => $r->user()->id, 'subject' => $d['subject']]);
        $t->messages()->create(['user_id' => $r->user()->id, 'body' => $d['body']]);

        return response()->json($t->load('messages'), 201);
    }

    public function showTicket(Request $r, SupportTicket $ticket)
    {
        abort_unless($ticket->user_id === $r->user()->id, 404);

        return $ticket->load('messages');
    }

    public function replyTicket(Request $r, SupportTicket $ticket)
    {
        abort_unless($ticket->user_id === $r->user()->id && $ticket->status !== 'closed', 404);
        $d = $r->validate(['body' => 'required|string|max:4000']);
        $ticket->messages()->create(['user_id' => $r->user()->id, 'body' => $d['body']]);
        $ticket->update(['status' => 'open']);

        return response()->json($ticket->load('messages'));
    }
}
