<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\{HasMedia, InteractsWithMedia};

class Banner extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('desktop')->singleFile();
        $this->addMediaCollection('mobile')->singleFile();
    }
}
