<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Concern;
use App\Models\Product;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoProductsSeeder extends Seeder
{
    private const PRODUCTS = [
        [
            'name' => 'Demo - Daily Gel Cleanser',
            'description' => 'DEMO ONLY. Replace this placeholder description with verified product and manufacturer details before selling.',
            'price' => 450,
            'compare_price' => 550,
            'category' => 'Face Wash and Cleanser',
            'concerns' => ['Acne', 'Sensitive Skin'],
            'is_new' => true,
            'is_recommended' => true,
            'color' => [191, 211, 198],
        ],
        [
            'name' => 'Demo - Hydrating Face Serum',
            'description' => 'DEMO ONLY. Replace this placeholder description with verified product and manufacturer details before selling.',
            'price' => 680,
            'compare_price' => null,
            'category' => 'Serums',
            'concerns' => ['Hydration'],
            'is_new' => true,
            'is_recommended' => true,
            'color' => [213, 190, 158],
        ],
        [
            'name' => 'Demo - Daily Sun Cream',
            'description' => 'DEMO ONLY. No SPF or protection claims are made. Replace with accurate, verified product details before selling.',
            'price' => 720,
            'compare_price' => 850,
            'category' => 'Sunscreen',
            'concerns' => ['Sun Protection'],
            'is_new' => false,
            'is_recommended' => true,
            'color' => [231, 207, 146],
        ],
        [
            'name' => 'Demo - Night Moisture Cream',
            'description' => 'DEMO ONLY. Replace this placeholder description with verified product and manufacturer details before selling.',
            'price' => 590,
            'compare_price' => null,
            'category' => 'Night Cream',
            'concerns' => ['Hydration', 'Hyperpigmentation'],
            'is_new' => false,
            'is_recommended' => false,
            'color' => [203, 181, 205],
        ],
        [
            'name' => 'Demo - Everyday Hair Oil',
            'description' => 'DEMO ONLY. Replace this placeholder description with verified product and manufacturer details before selling.',
            'price' => 390,
            'compare_price' => null,
            'category' => 'Hair Oil',
            'concerns' => ['Hair Fall'],
            'is_new' => true,
            'is_recommended' => false,
            'color' => [196, 177, 139],
        ],
        [
            'name' => 'Demo - Soft Tint Lip Color',
            'description' => 'DEMO ONLY. Replace this placeholder description with verified product and manufacturer details before selling.',
            'price' => 350,
            'compare_price' => null,
            'category' => 'Lipstick',
            'concerns' => [],
            'is_new' => false,
            'is_recommended' => false,
            'color' => [207, 153, 157],
        ],
        [
            'name' => 'Demo - Hydration Routine Set',
            'description' => 'DEMO ONLY bundle. Replace with the actual included products, pricing, and verified details before selling.',
            'price' => 990,
            'compare_price' => 1250,
            'category' => 'Moisturizers',
            'concerns' => ['Hydration'],
            'is_new' => false,
            'is_recommended' => true,
            'is_combo' => true,
            'color' => [174, 193, 204],
        ],
    ];

    public function run(): void
    {
        if (! function_exists('imagecreatetruecolor')) {
            throw new RuntimeException('The GD extension is required to generate demo product artwork.');
        }

        Category::whereNull('parent_id')
            ->whereIn('name', ['Skincare', 'Hair', 'Makeup'])
            ->update(['is_trending' => true]);

        foreach (self::PRODUCTS as $item) {
            $category = Category::where('name', $item['category'])->firstOrFail();
            $product = Product::firstOrCreate(
                ['name' => $item['name']],
                [
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'compare_price' => $item['compare_price'],
                    'stock' => 0,
                    'is_active' => true,
                    'is_new' => $item['is_new'],
                    'is_combo' => $item['is_combo'] ?? false,
                    'is_recommended' => $item['is_recommended'],
                ],
            );

            $product->categories()->syncWithoutDetaching([$category->id]);

            $concernIds = Concern::whereIn('name', $item['concerns'])->pluck('id');
            $product->concerns()->syncWithoutDetaching($concernIds);

            if ($product->getMedia('images')->isEmpty()) {
                $path = $this->createArtwork($item['name'], $item['color']);

                try {
                    $product
                        ->addMedia($path)
                        ->usingFileName($product->slug.'-demo.png')
                        ->toMediaCollection('images');
                } finally {
                    if (is_file($path)) {
                        unlink($path);
                    }
                }
            }
        }
    }

    /**
     * Generate original placeholder pack art so demo catalog images are never
     * mistaken for photographs of real products.
     *
     * @param  array{0: int, 1: int, 2: int}  $color
     */
    private function createArtwork(string $name, array $color): string
    {
        $image = imagecreatetruecolor(600, 600);
        $background = imagecolorallocate($image, 248, 238, 236);
        $accent = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        $dark = imagecolorallocate($image, 73, 55, 53);
        $white = imagecolorallocate($image, 255, 255, 255);
        $muted = imagecolorallocate($image, 130, 118, 109);

        imagefill($image, 0, 0, $background);
        imagefilledellipse($image, 300, 270, 430, 430, $white);
        imagefilledrectangle($image, 250, 145, 350, 190, $dark);
        imagefilledrectangle($image, 220, 180, 380, 430, $accent);
        imagefilledellipse($image, 300, 205, 160, 55, $accent);
        imagefilledellipse($image, 300, 410, 160, 55, $accent);
        imagefilledrectangle($image, 240, 270, 360, 350, $white);

        $label = 'DEMO ITEM';
        imagestring($image, 5, (600 - imagefontwidth(5) * strlen($label)) / 2, 282, $label, $dark);

        $words = explode(' ', str_replace('Demo - ', '', $name));
        $line = '';
        $lines = [];
        foreach ($words as $word) {
            $candidate = trim($line.' '.$word);
            if (strlen($candidate) > 28 && $line !== '') {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $candidate;
            }
        }
        if ($line !== '') {
            $lines[] = $line;
        }

        foreach (array_slice($lines, 0, 2) as $index => $text) {
            $x = (int) ((600 - imagefontwidth(4) * strlen($text)) / 2);
            imagestring($image, 4, $x, 305 + ($index * 18), $text, $dark);
        }
        $caption = 'SAMPLE CATALOG ART';
        imagestring($image, 3, (600 - imagefontwidth(3) * strlen($caption)) / 2, 520, $caption, $muted);

        $path = tempnam(sys_get_temp_dir(), 'demo-product-');
        if ($path === false) {
            imagedestroy($image);
            throw new RuntimeException('Unable to allocate a temporary file for demo product artwork.');
        }

        $written = imagepng($image, $path);
        imagedestroy($image);

        if (! $written) {
            unlink($path);
            throw new RuntimeException('Unable to generate demo product artwork.');
        }

        return $path;
    }
}
