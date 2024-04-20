<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MessagePolicy
{
    public function view(User $user, Message $message)
    {
        return ($user->is_admin && $message->to_id === $user->id) ||
            (!$user->is_admin && $message->from_id === $user->id) ||
            ($message->to_id === $user->id) ||
            ($message->from_id === $user->id);
    }

    public function create(User $user)
    {
        return true;
    }
}
