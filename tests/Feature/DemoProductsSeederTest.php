<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\DemoProductsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoProductsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_products_are_labeled_inactive_for_checkout_and_seed_idempotently(): void
    {
        $this->seed();
        $this->seed(DemoProductsSeeder::class);

        $this->assertSame(7, Product::where('name', 'like', 'Demo - %')->count());
        $this->assertDatabaseHas('products', [
            'name' => 'Demo - Daily Sun Cream',
            'stock' => 0,
            'is_new' => false,
        ]);
        $this->assertDatabaseHas('products', [
            'name' => 'Demo - Hydration Routine Set',
            'is_combo' => true,
        ]);
        $this->assertDatabaseCount('media', 7);
        $this->assertSame(
            ['Hair', 'Makeup', 'Skincare'],
            Category::where('is_trending', true)->orderBy('name')->pluck('name')->all(),
        );

        $this->seed(DemoProductsSeeder::class);

        $this->assertSame(7, Product::where('name', 'like', 'Demo - %')->count());
        $this->assertDatabaseCount('media', 7);
    }
}
