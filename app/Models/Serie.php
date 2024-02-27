<?php

namespace App\Models;

use App\Traits\HasCreatorAndUpdater;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Serie extends Model
{
    use HasFactory, HasCreatorAndUpdater, SoftDeletes;

    protected $table = 'series';

    protected $guarded = [];

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
            $q->where('title', 'like', "%" . request('search'). "%")
            ->orWhere('description', 'like', "%" . request('search'). "%");
        }
    }
}
