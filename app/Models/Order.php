<?php

namespace App\Models;

use App\Traits\HasCreatorAndUpdater;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory, HasCreatorAndUpdater, SoftDeletes;

    protected $table = 'orders';

    protected $guarded = [];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function owner(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $attributes['created_by'] === Auth::id() ? "Me" : $this->user->name;
            }
        );
    }

    public function scopeFilterOn($q)
    {
        if (request('search')) {
            $q->whereHas('customer', function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                    ->orWhere('username', 'like', '%' . request('search') . '%');
            });
        }

        if (request('start_date') || request('end_date')) {
            $start_date = request('start_date') ? Carbon::parse(request('start_date')) : now()->startOfMonth();

            $end_date = request('end_date') ? Carbon::parse(request('end_date')) : now();

            $q->whereBetween('created_at', [$start_date, $end_date]);
        }
    }

    // public function
}
