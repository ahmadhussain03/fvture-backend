<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class NavigationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerNavigationGroups([
                'Content Management' => [
                    'sort' => 1,
                    'icon' => 'heroicon-o-document-text',
                ],
                'Users & Roles' => [
                    'sort' => 2,
                    'icon' => 'heroicon-o-users',
                ],
            ]);
        });
    }
}
