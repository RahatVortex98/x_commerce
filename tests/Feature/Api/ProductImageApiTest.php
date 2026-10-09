<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductImageApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_token_can_upload_product_images_to_the_gallery(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $product = Product::create([
            'name' => 'Daily Face Cream',
            'price' => 250,
            'stock' => 5,
            'is_active' => true,
        ]);
        $token = $admin->createToken(
            'catalog-uploader',
            ['product-images:write'],
            now()->addDays(90),
        )->plainTextToken;

        foreach (['front.jpg', 'back.jpg'] as $fileName) {
            $this->withToken($token)->postJson(route('api.products.images.store', $product), [
                'image' => UploadedFile::fake()->image($fileName, 120, 120),
            ])
                ->assertCreated()
                ->assertJsonPath('data.product.slug', $product->slug)
                ->assertJsonPath('data.image.file_name', $fileName)
                ->assertJsonStructure([
                    'data' => [
                        'product' => ['id', 'slug'],
                        'image' => ['id', 'file_name', 'url', 'thumbnail_url', 'large_url'],
                    ],
                ]);
        }

        $this->assertDatabaseCount('media', 2);
        $this->assertDatabaseHas('media', [
            'model_id' => $product->id,
            'collection_name' => 'images',
            'file_name' => 'front.jpg',
        ]);
        $this->assertDatabaseHas('media', [
            'model_id' => $product->id,
            'collection_name' => 'images',
            'file_name' => 'back.jpg',
        ]);

        $productPage = $this->get(route('product', $product))->assertOk();
        foreach ($product->fresh()->getMedia('images') as $image) {
            $productPage->assertSee($image->getUrl('large'), false);
        }
    }

    public function test_product_image_api_requires_an_admin_token_with_upload_ability(): void
    {
        $product = Product::create([
            'name' => 'Daily Face Cream',
            'price' => 250,
            'stock' => 5,
            'is_active' => true,
        ]);
        $payload = ['image' => UploadedFile::fake()->image('photo.jpg')];

        $this->postJson(route('api.products.images.store', $product), $payload)
            ->assertUnauthorized();

        $admin = $this->admin();
        Sanctum::actingAs($admin, []);
        $this->postJson(route('api.products.images.store', $product), $payload)
            ->assertForbidden();

        $regularUser = User::factory()->create();
        Sanctum::actingAs($regularUser, ['product-images:write']);
        $this->postJson(route('api.products.images.store', $product), $payload)
            ->assertForbidden();
    }

    public function test_product_image_api_rejects_non_image_uploads(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $product = Product::create([
            'name' => 'Daily Face Cream',
            'price' => 250,
            'stock' => 5,
            'is_active' => true,
        ]);
        Sanctum::actingAs($admin, ['product-images:write']);

        $this->postJson(route('api.products.images.store', $product), [
            'image' => UploadedFile::fake()->create('notes.txt', 10, 'text/plain'),
        ])->assertUnprocessable()->assertJsonValidationErrors('image');

        $this->assertDatabaseCount('media', 0);
    }

    private function admin(): User
    {
        Role::findOrCreate('admin');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }
}
