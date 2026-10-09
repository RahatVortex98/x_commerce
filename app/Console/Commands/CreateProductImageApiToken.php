<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateProductImageApiToken extends Command
{
    protected $signature = 'api:product-image-token
                            {email : Email address of the administrator}
                            {--name=product-image-uploader : Name used to identify this API client}
                            {--revoke : Revoke existing tokens with this name instead of creating one}';

    protected $description = 'Create or revoke an administrator token for product image uploads';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user || ! $user->hasRole('admin')) {
            $this->error('The specified user does not exist or is not an administrator.');

            return self::FAILURE;
        }

        $name = $this->option('name');
        if (! is_string($name) || trim($name) === '') {
            $this->error('Token name cannot be empty.');

            return self::FAILURE;
        }

        if ($this->option('revoke')) {
            $count = $user->tokens()->where('name', $name)->delete();
            $this->info("Revoked {$count} product-image API token(s) named [{$name}].");

            return self::SUCCESS;
        }

        $token = $user->createToken(
            $name,
            ['product-images:write'],
            now()->addDays(90),
        );

        $this->warn('Copy this token now. It will not be shown again and expires in 90 days.');
        $this->line($token->plainTextToken);

        return self::SUCCESS;
    }
}
