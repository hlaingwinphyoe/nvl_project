<?php

namespace App\Models;

use Carbon\Carbon;
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

        if (request('start_date') || request('end_date')) {
            $start_date = request('start_date') ? Carbon::parse(request('start_date')) : now()->startOfMonth();

            $end_date = request('end_date') ? Carbon::parse(request('end_date')) : now();

            $q->whereBetween('created_at', [$start_date, $end_date]);
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
