<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function conversation(Request $request, User $user)
    {
        $loggedUserId = auth()->id();

        $messages = Message::where(function ($query) use ($loggedUserId, $user) {
            $query->where('from_id', $loggedUserId)->where('to_id', $user->id);
        })->orWhere(function ($query) use ($loggedUserId, $user) {
            $query->where('from_id', $user->id)->where('to_id', $loggedUserId);
        })->orderBy('created_at', 'asc')->get();


        return response()->json([
            'loggedUserId' => $loggedUserId,
            'messages' => $messages,
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'to_id' => 'required|exists:users,id',
            'body' => 'required|string',
        ]);

        $message = Message::create([
            'from_id' => auth()->id(),
            'to_id' => $request->to_id,
            'body' => $request->body,
        ]);

        return response()->json($message, 201);
    }
}
