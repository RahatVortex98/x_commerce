<?php
namespace Database\Seeders;

use App\Models\{Category, Concern, User};
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        Role::findOrCreate('admin');
        Role::findOrCreate('manager');

        User::firstOrCreate(['email' => 'admin@x.test'], [
            'name' => 'Admin', 'password' => bcrypt('password'),
        ])->assignRole('admin');

        $tree = [
            'Makeup' => ['Eyes' => ['Eye Shadow', 'Eyeliner', 'Kajal', 'Mascara'],
                         'Face' => ['Blush', 'Concealer', 'Face Foundation', 'Face Primer'],
                         'Lips Care' => ['Lip Balm', 'Lip Gloss', 'Lipstick']],
            'Skincare' => ['Cleansers and Toners' => ['Face Wash and Cleanser', 'Micellar Water', 'Toners and Mists'],
                           'Moisturizers' => ['Face Oils', 'Night Cream'],
                           'Serums & Essence' => ['Essence', 'Serums'],
                           'Suncare' => ['Sunscreen']],
            'Hair' => ['Hair Care' => ['Shampoo', 'Hair-Conditioner', 'Hair Oil', 'Hair Serum'],
                       'Hair Styling' => ['Gels and Waxes', 'Hair Spray']],
            'Essentials' => ['Bath and Shower' => ['Soaps', 'Shower Gels and Body Wash'],
                             'Body Care' => ['Body Butter', 'Body Lotions Creams'],
                             'Oral Care' => ['Toothpaste', 'Mouthwash']],
            'Fragrance' => ['Fragrance Men' => ['Men Perfumes (EDT and EDP)'],
                            'Fragrance Women' => ['Women Perfumes (EDT and EDP)']],
            'Baby Care' => ['Baby Care' => ['Baby Oil', 'Baby Powder']],
        ];

        $i = 0;
        foreach ($tree as $top => $groups) {
            $t = Category::firstOrCreate(
                ['name' => $top, 'parent_id' => null],
                ['sort' => $i++],
            );
            foreach ($groups as $group => $leaves) {
                $g = Category::firstOrCreate(['name' => $group, 'parent_id' => $t->id]);
                foreach ($leaves as $leaf) {
                    Category::firstOrCreate(['name' => $leaf, 'parent_id' => $g->id]);
                }
            }

            foreach ([
                'Acne' => 'face',
                'Hyperpigmentation' => 'face',
                'Sun Protection' => 'face',
                'Dark Circles' => 'face',
                'Hydration' => 'skin',
                'Sensitive Skin' => 'skin',
                'Hair Fall' => 'hair',
                'Dry Scalp' => 'hair',
            ] as $name => $type) {
                Concern::firstOrCreate(['name' => $name], ['type' => $type]);
            }
        }
    }
}