<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the initial Filament administrator.
     */
    public function run(): void
    {
        $name = config('initial_admin.name');
        $email = config('initial_admin.email');
        $password = config('initial_admin.password');

        if (! is_string($email) || blank($email)) {
            throw new RuntimeException('INITIAL_ADMIN_EMAIL must be configured before seeding.');
        }

        if (! is_string($password) || blank($password)) {
            throw new RuntimeException('INITIAL_ADMIN_PASSWORD must be configured before seeding.');
        }

        $user = User::query()->firstOrNew([
            'email' => $email,
        ]);

        if (! $user->exists) {
            $user->name = is_string($name) && filled($name) ? $name : 'Administrator';
            $user->password = $password;
        }

        $user->is_admin = true;
        $user->email_verified_at ??= now();
        $user->save();
    }
}
