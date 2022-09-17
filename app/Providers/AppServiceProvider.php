<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Prevent Specified key was too long error
        Schema::defaultStringLength(191);

        Blade::component('components.buttonAdd', 'buttonAdd');
        Blade::component('components.buttonUpdate', 'buttonUpdate');
        Blade::component('components.buttonRemove', 'buttonRemove');
        Blade::component('components.buttonRestore', 'buttonRestore');
    
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
