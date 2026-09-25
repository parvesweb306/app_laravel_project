<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_name',
        'pairing_code',
        'device_token',
        'is_locked',
        'unlock_code_hash',
        'unlock_code_expires_at',
        'last_seen',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'last_seen' => 'datetime',
        'unlock_code_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}