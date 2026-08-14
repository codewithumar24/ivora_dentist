<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    protected $fillable = ['email', 'name', 'is_active', 'unsubscribe_token', 'subscribed_at'];

    protected $casts = ['is_active' => 'boolean', 'subscribed_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (self $subscriber) {
            $subscriber->unsubscribe_token ??= Str::random(48);
            $subscriber->subscribed_at ??= now();
        });
    }
}
