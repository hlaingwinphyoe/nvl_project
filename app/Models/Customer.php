<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'phone',
        'is_vip',
        'is_ban'
    ];

    public function scopeFilterOn($q)
    {
        if (request('search')) {
            $q->where('name', 'like', '%' . request('search') . '%')
                ->orWhere('username', 'like', '%' . request('search') . '%');
        }
    }

    public function scopeNotBan($q)
    {
        $q->where('is_ban', 0);
    }

    public function scopeBanned($q)
    {
        $q->where('is_ban', 1);
    }
}
