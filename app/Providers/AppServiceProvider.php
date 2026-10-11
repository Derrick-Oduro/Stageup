<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-admin-dashboard', function (User $user): bool {
            return $user->hasRole('admin');
        });

        User::created(function (User $user): void {
            $user->profile()->firstOrCreate(
                [],
                [
                    'display_name' => $user->name,
                    'timezone' => 'UTC',
                    'is_public' => true,
                ],
            );

            if ($user->roles()->exists()) {
                return;
            }

            if (! Role::where('name', 'registered_user')->where('guard_name', 'web')->exists()) {
                return;
            }

            $user->assignRole('registered_user');
        });
    }
}
