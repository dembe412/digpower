<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    public function messages(Request $request)
    {
        $type = $request->input('type', 'group');
        $user = $request->user();

        if ($type === 'private') {
            if (! $user) {
                return response()->json(['error' => 'Authentication required for private chat.'], 401);
            }

            if ($user->is_admin && $request->filled('target_user_id')) {
                $targetId = (int) $request->input('target_user_id');
                $room = 'user_'.$targetId;
            } else {
                $room = 'user_'.$user->id;
            }
        } else {
            $type = 'group';
            $room = 'community';
        }

        $query = ChatMessage::with(['user', 'replyTo.user'])
            ->where('type', $type)
            ->where('room', $room);

        if ($request->filled('after_id')) {
            $query->where('id', '>', (int) $request->input('after_id'))->orderBy('id', 'asc');
            $messages = $query->limit(50)->get();
        } else {
            $messages = $query->latest('id')->limit(60)->get()->reverse()->values();
        }

        $formatted = $messages->map(fn (ChatMessage $m) => $m->toResponseArray($user));

        return response()->json([
            'type' => $type,
            'room' => $room,
            'messages' => $formatted,
            'meta' => [
                'count' => $formatted->count(),
                'online_estimate' => max(42, 120 + ($type === 'group' ? 18 : 1)),
            ],
        ]);
    }

    public function send(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Authentication required.'], 401);
        }

        $type = $request->input('type', 'group');
        if (! in_array($type, ['group', 'private'], true)) {
            $type = 'group';
        }

        $request->validate([
            'message' => 'nullable|string|max:2000',
            'image' => 'nullable|image|max:8192',
            'reply_to_id' => 'nullable|integer|exists:chat_messages,id',
            'target_user_id' => 'nullable|integer|exists:users,id',
        ]);

        if (! $request->filled('message') && ! $request->hasFile('image')) {
            throw ValidationException::withMessages(['message' => 'Please enter a message or attach an image.']);
        }

        $recipientId = null;
        if ($type === 'private') {
            if ($user->is_admin && $request->filled('target_user_id')) {
                $recipientId = (int) $request->input('target_user_id');
                $room = 'user_'.$recipientId;
            } else {
                $room = 'user_'.$user->id;
                // Default recipient is staff/admin
                $recipientId = User::where('is_admin', true)->value('id');
            }
        } else {
            $room = 'community';
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chat_attachments', 'public');
            $imageUrl = Storage::url($path);
        }

        // Auto extract mentions from message
        $mentions = [];
        if ($msg = $request->input('message')) {
            if (preg_match_all('/@([a-zA-Z0-9_\-\.]+)/', $msg, $matches)) {
                $mentions = array_values(array_unique($matches[1]));
            }
        }

        $chatMessage = ChatMessage::create([
            'type' => $type,
            'room' => $room,
            'user_id' => $user->id,
            'recipient_id' => $recipientId,
            'reply_to_id' => $request->input('reply_to_id'),
            'message' => $request->input('message'),
            'image_url' => $imageUrl,
            'mentions' => $mentions,
            'is_staff' => (bool) $user->is_admin,
        ]);

        $chatMessage->load(['user', 'replyTo.user']);

        return response()->json([
            'message' => 'Sent successfully.',
            'chat_message' => $chatMessage->toResponseArray($user),
        ], 201);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $message = ChatMessage::findOrFail($id);

        if (! $user->is_admin && $message->user_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        $message->delete();

        return response()->json(['message' => 'Message deleted.']);
    }

    public function privateThreads(Request $request)
    {
        $user = $request->user();
        if (! $user->is_admin) {
            abort(403, 'Administrator access required.');
        }

        // Group private messages by room
        $rooms = ChatMessage::where('type', 'private')
            ->selectRaw('room, max(id) as last_id, count(*) as total_messages')
            ->groupBy('room')
            ->orderByDesc('last_id')
            ->limit(30)
            ->get();

        $threads = [];
        foreach ($rooms as $item) {
            $userId = (int) str_replace('user_', '', $item->room);
            $client = User::find($userId);
            $lastMsg = ChatMessage::with('user')->find($item->last_id);

            if ($client && $lastMsg) {
                $threads[] = [
                    'room' => $item->room,
                    'user_id' => $client->id,
                    'user_name' => $client->name ?: $client->maskedIdentity(),
                    'user_avatar' => $client->avatar_url,
                    'user_email' => $client->maskedIdentity(),
                    'total_messages' => $item->total_messages,
                    'last_message' => $lastMsg->message ?: '[Image attachment]',
                    'last_sender' => $lastMsg->is_staff ? 'Staff' : 'Client',
                    'updated_at' => $lastMsg->created_at?->diffForHumans(null, true).' ago',
                ];
            }
        }

        return response()->json(['threads' => $threads]);
    }

    public function members(Request $request)
    {
        $tags = ['Admin', 'Support', 'Everyone'];
        $recentUsers = User::where('is_active', true)
            ->latest('id')
            ->limit(15)
            ->get()
            ->map(fn (User $u) => $u->name ?: $u->maskedIdentity())
            ->filter()
            ->values()
            ->all();

        return response()->json(['members' => array_values(array_unique(array_merge($tags, $recentUsers)))]);
    }
}
