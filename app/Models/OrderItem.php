<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['title'];

    public function serie()
    {
        return $this->belongsTo(Serie::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getTitleAttribute()
    {
        return $this->serie ? $this->serie->title : '';
    }
}
