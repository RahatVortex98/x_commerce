<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\{HasSlug, SlugOptions};

class Category extends Model
{
    use HasSlug;
    protected $guarded = [];
    protected $casts = ['is_active' => 'bool', 'is_trending' => 'bool'];

    public function getSlugOptions(): SlugOptions
    { return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug'); }
    public function getRouteKeyName(): string { return 'slug'; }

    public function parent()   { return $this->belongsTo(self::class, 'parent_id'); }
    public function children() { return $this->hasMany(self::class, 'parent_id')->where('is_active', true)->orderBy('sort'); }
    public function products() { return $this->belongsToMany(Product::class, 'category_products'); }

    public function descendantIds(): array
    {
        $ids = $level = [$this->id];
        while ($level = static::whereIn('parent_id', $level)->pluck('id')->all()) {
            $ids = array_merge($ids, $level);
        }
        return $ids;
    }
}