<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\{HasMedia, InteractsWithMedia};
use Spatie\Sluggable\{HasSlug, SlugOptions};

class Brand extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia;
    protected $guarded = [];

    public function getSlugOptions(): SlugOptions
    { return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug'); }
    public function getRouteKeyName(): string { return 'slug'; }
    public function registerMediaCollections(): void
    { $this->addMediaCollection('tile')->singleFile(); }   // brand card image
    public function products() { return $this->hasMany(Product::class); }
}