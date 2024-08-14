<?php

namespace Btal\PurchaseRequisition;

use Illuminate\Support\ServiceProvider;

class PurchaseRequisitionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'purchaserequisition');

        $this->commands([
            ##InstallationCommandClass##
        ]);
        $this->publishes([
            __DIR__ . '/../stubs/assets' => public_path('vendor/BTAL/purchaserequisition/assets'),
        ], 'public');
    }
    
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/sidebar.php', 'navigation.purchaserequisition.menus');
        
    }

}
