<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];



    public function scopeAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeUser($query)
    {
        return $query->where('is_admin', false);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'to_id');
    }
    public function getTable()
    {
        return config('nova-chat.recipients_table', 'users');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'from_id', 'id')
            ->where(function ($query) {
                $query->where('from_id', auth()->user()->id)
                    ->orWhere('to_id', auth()->user()->id)
                    ->orWhere(function ($query) {
                        $query->where('to_id', auth()->user()->id)
                            ->orWhere('from_id', auth()->user()->id);
                    });
            })
            ->orWhere(function ($query) {
                $query->where('to_id', $this->id)
                    ->where('from_id', auth()->user()->id);
            })
            ->orWhere(function ($query) {
                $query->where('from_id', auth()->user()->id)
                    ->where('to_id', $this->id);
            });
    }

    public function unreadMessagesCount()
    {
        return $this->receivedMessages()->whereNull('seen_at')->count();
    }

    public function getChatMessagesAttribute() {
        // Assuming you want to fetch messages for the admin user with ID 1
        $adminId = 1;
        return $this->receivedMessages()->where('from_id', $adminId)
                ->get(['body', 'created_at'])
                ->merge($this->sentMessages()->where('to_id', $adminId)
                ->get(['body', 'created_at']))
                ->sortBy('created_at');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'from_id');
    }

}

