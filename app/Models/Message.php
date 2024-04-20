<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'from_id',
        'to_id',
        'body',
        'seen_at',
    ];
    protected $dates = [
        'seen_at',
    ];

    protected $with = [
        'sender', 'recipient'
    ];

    protected static function booted()
    {
        static::creating(function (self $model) {
            $model->from_id = auth()->user()->id;
        });
    }

    public function getTable()
    {
        return config('nova-chat.messages_table', 'messages');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'from_id', 'id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'to_id', 'id');
    }

    public function scopeUnread($query)
    {
        $query->whereNull('seen_at')->where('from_id', '!=', auth()->user()->id);
    }

    public function scopeMines($query)
    {
        $query->where('from_id', auth()->user()->id)
            ->orWhere('to_id', auth()->user()->id);
    }

    public function scopeWithRecipient(Builder $query, $id)
    {
        $query
            ->where('from_id', auth()->user()->id)
            ->where('to_id', $id)
            ->orWhere(function ($query) use ($id) {
                $query->where('to_id', auth()->user()->id)
                    ->where('from_id', $id);
            });
    }

    public static function markSeenWithRecipientUntil(User $recipient, int $id)
    {
        return static::query()
            ->where('id', '<=', $id)
            ->whereNull('seen_at')
            ->where('from_id', $recipient->id)
            ->orWhere('to_id', $recipient->id)
            ->update([
                'seen_at' => now()
            ]);
    }

    public function scopeFromRecipient($query, User $recipient)
    {
        $query->where('from_id', $recipient->id);
    }

    public function scopeToOrFromAdmin($query)
    {
        return $query->whereHas('sender', function ($query) {
            $query->where('is_admin', true);
        })
            ->orWhereHas('recipient', function ($query) {
                $query->where('is_admin', true);
            });
    }
}
