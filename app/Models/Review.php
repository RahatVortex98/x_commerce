<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $guarded = [];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'bool',
        'is_featured' => 'bool',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
