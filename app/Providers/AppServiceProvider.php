<?php

namespace App\Providers;

use App\Models\User;
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
        User::created(function (User $user): void {
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
