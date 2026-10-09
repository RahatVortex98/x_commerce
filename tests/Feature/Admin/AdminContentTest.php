<?php

namespace Tests\Feature\Admin;

use App\Models\{Brand, Category, Product, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_routes_require_authentication_and_admin_role(): void
    {
        $this->get('/admin')->assertRedirect('/login');

        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_manage_store_content(): void
    {
        $user = User::factory()->create();
        Role::findOrCreate('admin');
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Your shop at a glance');

        $this->actingAs($user)
            ->get('/admin/categories/create')
            ->assertOk()
            ->assertSee('Category name');

        foreach ([
            '/admin/products',
            '/admin/products/create',
            '/admin/brands',
            '/admin/brands/create',
            '/admin/banners',
            '/admin/banners/create',
            '/admin/concerns',
            '/admin/concerns/create',
            '/admin/reviews',
            '/admin/reviews/create',
        ] as $path) {
            $this->actingAs($user)->get($path)->assertOk();
        }

        $this->actingAs($user)
            ->post('/admin/categories', [
                'name' => 'New Category',
                'sort' => 1,
                'is_active' => '1',
            ])
            ->assertRedirect('/admin/categories');

        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'slug' => 'new-category',
        ]);

        $this->actingAs($user)
            ->post('/admin/brands', [
                'name' => 'New Brand',
                'offer_label' => '20% OFF',
                'sort' => 0,
                'is_top' => '1',
            ])
            ->assertRedirect('/admin/brands');

        $brand = Brand::where('name', 'New Brand')->firstOrFail();
        $category = Category::where('name', 'New Category')->firstOrFail();

        $this->actingAs($user)
            ->post('/admin/concerns', [
                'name' => 'Acne',
                'type' => 'face',
                'sort' => 0,
            ])
            ->assertRedirect('/admin/concerns');
        $this->assertDatabaseHas('concerns', ['name' => 'Acne', 'type' => 'face']);

        $this->actingAs($user)
            ->post('/admin/products', [
                'name' => 'New Product',
                'brand_id' => $brand->id,
                'description' => 'Product description',
                'price' => '12.50',
                'compare_price' => '15.00',
                'stock' => 8,
                'categories' => [$category->id],
                'concerns' => [\App\Models\Concern::where('name', 'Acne')->value('id')],
                'is_new' => '1',
                'is_active' => '1',
                'is_recommended' => '1',
            ])
            ->assertRedirect('/admin/products');

        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'brand_id' => $brand->id,
            'is_recommended' => true,
        ]);
        $this->assertDatabaseHas('category_products', [
            'category_id' => $category->id,
        ]);
        $this->assertDatabaseHas('concern_products', [
            'concern_id' => \App\Models\Concern::where('name', 'Acne')->value('id'),
            'product_id' => Product::where('name', 'New Product')->value('id'),
        ]);

        $this->actingAs($user)
            ->post('/admin/banners', [
                'title' => 'Spring Sale',
                'link' => '/collections/skincare',
                'sort' => 0,
                'is_active' => '1',
            ])
            ->assertRedirect('/admin/banners');

        $this->assertDatabaseHas('banners', ['title' => 'Spring Sale']);

        $this->actingAs($user)
            ->post('/admin/reviews', [
                'product_id' => Product::where('name', 'New Product')->value('id'),
                'reviewer_name' => 'Happy Customer',
                'rating' => 5,
                'body' => 'A lovely product.',
                'is_approved' => '1',
                'is_featured' => '1',
            ])
            ->assertRedirect('/admin/reviews');
        $this->assertDatabaseHas('reviews', [
            'reviewer_name' => 'Happy Customer',
            'is_approved' => true,
            'is_featured' => true,
        ]);

        $this->actingAs($user)->get('/dashboard')->assertRedirect('/admin');

        $this->actingAs($user)->get('/admin')
            ->assertOk()
            ->assertSee('Shop by concern')
            ->assertSee('Customer reviews')
            ->assertDontSee(route('dashboard'), false);
    }
}
