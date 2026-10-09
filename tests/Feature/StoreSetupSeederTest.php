<?php

namespace Tests\Feature;

use App\Models\{Category, Concern, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Artisan, Hash};
use Tests\TestCase;

class StoreSetupSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeder_creates_the_admin_and_starter_categories_idempotently(): void
    {
        Artisan::call('db:seed');

        $admin = User::where('email', 'admin@x.test')->firstOrFail();

        $this->assertTrue(Hash::check('password', $admin->password));
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertSame(1, Category::where('name', 'Makeup')->whereNull('parent_id')->count());
        $this->assertSame('face', Concern::where('name', 'Acne')->value('type'));
        $this->assertSame('face', Concern::where('name', 'Hyperpigmentation')->value('type'));
        $this->assertSame('face', Concern::where('name', 'Sun Protection')->value('type'));

        Artisan::call('db:seed');

        $this->assertSame(1, User::where('email', 'admin@x.test')->count());
        $this->assertSame(1, Category::where('name', 'Makeup')->whereNull('parent_id')->count());
        $this->assertSame(1, Concern::where('name', 'Acne')->count());
    }
}
