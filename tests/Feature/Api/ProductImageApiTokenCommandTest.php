<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductImageApiTokenCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_issues_scoped_expiring_tokens_and_can_revoke_them(): void
    {
        Role::findOrCreate('admin');
        $admin = User::factory()->create(['email' => 'admin@example.test']);
        $admin->assignRole('admin');

        $exitCode = Artisan::call('api:product-image-token', [
            'email' => $admin->email,
            '--name' => 'catalog-uploader',
        ]);
        $this->assertSame(Command::SUCCESS, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('expires in 90 days', $output);

        $token = $admin->tokens()->where('name', 'catalog-uploader')->firstOrFail();
        $this->assertSame(['product-images:write'], $token->abilities);
        $this->assertNotNull($token->expires_at);
        $this->assertTrue($token->expires_at->isFuture());

        $exitCode = Artisan::call('api:product-image-token', [
            'email' => $admin->email,
            '--name' => 'catalog-uploader',
            '--revoke' => true,
        ]);
        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token->id]);
    }

    public function test_command_refuses_to_issue_a_token_for_a_non_admin(): void
    {
        User::factory()->create(['email' => 'customer@example.test']);

        $exitCode = Artisan::call('api:product-image-token', [
            'email' => 'customer@example.test',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
