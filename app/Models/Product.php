<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Builder, Model};
use Spatie\MediaLibrary\{HasMedia, InteractsWithMedia};
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\{HasSlug, SlugOptions};

class Product extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia;
    protected $guarded = [];
    protected $casts = [
        'is_combo' => 'bool',
        'is_new' => 'bool',
        'is_active' => 'bool',
        'is_recommended' => 'bool',
    ];

    public function getSlugOptions(): SlugOptions
    { return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug'); }
    public function getRouteKeyName(): string { return 'slug'; }

    public function registerMediaCollections(): void
    { $this->addMediaCollection('images'); }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(400)->height(400)->format('webp')->nonQueued();
        $this->addMediaConversion('large')->width(1000)->format('webp')->nonQueued();
    }

    public function brand()       { return $this->belongsTo(Brand::class); }
    public function variants()    { return $this->hasMany(ProductVariant::class); }
    public function categories()  { return $this->belongsToMany(Category::class, 'category_products'); }
    public function concerns()    { return $this->belongsToMany(Concern::class, 'concern_products'); }
    public function ingredients() { return $this->belongsToMany(Ingredient::class, 'ingredient_products'); }
    public function reviews()     { return $this->hasMany(Review::class)->where('is_approved', true); }
    public function customerReviews() { return $this->hasMany(Review::class); }

    public function getDiscountPercentAttribute(): ?int
    {
        return $this->compare_price > $this->price
            ? (int) round((1 - $this->price / $this->compare_price) * 100) : null;
    }
    public function getInStockAttribute(): bool
    {
        return $this->variants->isNotEmpty()
            ? $this->variants->sum('stock') > 0
            : $this->stock > 0;
    }

    // scopes (used by Spatie QueryBuilder filters)
    public function scopeActive(Builder $q) { $q->where('is_active', true); }
    public function scopeInStock(Builder $q) { $q->where('stock', '>', 0)->orWhereHas('variants', fn ($v) => $v->where('stock', '>', 0)); }
    public function scopePriceBetween(Builder $q, $min, $max) { $q->whereBetween('price', [$min, $max]); }
    public function scopeCategory(Builder $q, $slug)
    {
        $ids = Category::where('slug', $slug)->firstOrFail()->descendantIds();
        $q->whereHas('categories', fn ($c) => $c->whereIn('categories.id', $ids));
    }
    public function scopeConcern(Builder $q, $slug)    { $q->whereHas('concerns', fn ($c) => $c->where('slug', $slug)); }
    public function scopeIngredient(Builder $q, $slug) { $q->whereHas('ingredients', fn ($c) => $c->where('slug', $slug)); }
}