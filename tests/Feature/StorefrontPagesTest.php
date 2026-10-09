<?php

namespace Tests\Feature;

use App\Models\{Category, Concern, Product, Review};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_and_catalog_pages_render_when_store_is_empty(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Feel good in your own skin.')
            ->assertSee('Trending categories')
            ->assertSee('Shop by concern')
            ->assertSee('New arrivals')
            ->assertSee('Beauty sets')
            ->assertSee('Recommended for you')
            ->assertSee('Special offers')
            ->assertSee('Our customers recommend')
            ->assertSee('Customer recommendations will appear here as reviews are added.');
        $this->get('/search')->assertOk()->assertSee('Nothing here just yet');

        $category = Category::create(['name' => 'Skincare', 'sort' => 0]);
        $this->get(route('category', $category))
            ->assertOk()
            ->assertSee('Skincare')
            ->assertSee('Nothing here just yet');
    }

    public function test_customer_can_browse_a_product_and_complete_checkout(): void
    {
        $category = Category::create(['name' => 'Skincare', 'sort' => 0]);
        $product = Product::create([
            'name' => 'Daily Face Cream',
            'description' => 'A simple daily moisturizer.',
            'price' => 250,
            'stock' => 5,
            'is_active' => true,
        ]);
        $product->categories()->attach($category);

        $this->get(route('product', $product))
            ->assertOk()
            ->assertSee('Daily Face Cream')
            ->assertSee('Add to bag');

        $this->withSession(['cart' => [
            $product->id.'-0' => [
                'product_id' => $product->id,
                'variant_id' => null,
                'qty' => 1,
            ],
        ]])->get('/cart')->assertOk()->assertSee('Shopping bag');

        $this->withSession(['cart' => [
            $product->id.'-0' => [
                'product_id' => $product->id,
                'variant_id' => null,
                'qty' => 1,
            ],
        ]])->get('/checkout')
            ->assertOk()
            ->assertSee('Delivery')
            ->assertSee('payment');

        $this->withSession(['cart' => [
            $product->id.'-0' => [
                'product_id' => $product->id,
                'variant_id' => null,
                'qty' => 1,
            ],
        ]])->post('/checkout', [
            'name' => 'Test Customer',
            'phone' => '01712345678',
            'address' => '1 Market Road, Dhaka',
            'zone' => 'dhaka',
            'payment_method' => 'cod',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('orders', ['name' => 'Test Customer', 'total' => 310]);
    }

    public function test_homepage_shows_merchandising_sections_and_concern_matched_products(): void
    {
        $category = Category::create([
            'name' => 'Trending Skincare',
            'sort' => 0,
            'is_active' => true,
            'is_trending' => true,
        ]);
        $product = Product::create([
            'name' => 'Acne Care Cream',
            'description' => 'Gentle care for blemish-prone skin.',
            'price' => 250,
            'compare_price' => 300,
            'stock' => 5,
            'is_active' => true,
            'is_combo' => true,
            'is_new' => true,
            'is_recommended' => true,
        ]);
        $product->categories()->attach($category);

        $concern = Concern::create(['name' => 'Acne', 'type' => 'face', 'sort' => 0]);
        $product->concerns()->attach($concern);

        Review::create([
            'product_id' => $product->id,
            'reviewer_name' => 'A Happy Customer',
            'rating' => 5,
            'body' => 'This made my routine feel simple.',
            'is_approved' => true,
            'is_featured' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Trending categories')
            ->assertSee('Trending Skincare')
            ->assertSee('Shop by concern')
            ->assertSee('Acne')
            ->assertSee('Acne Care Cream')
            ->assertSee('New arrivals')
            ->assertSee('Beauty sets')
            ->assertSee('Recommended for you')
            ->assertSee('Special offers')
            ->assertSee('Our customers recommend')
            ->assertSee('A Happy Customer');
    }
}
