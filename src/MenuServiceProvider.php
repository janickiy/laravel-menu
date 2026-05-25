<?php

namespace Harimayco\Menu;

use Harimayco\Menu\Controllers\MenuController;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/Views', 'wmenu');
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'wmenu');

        $this->publishes([
            __DIR__ . '/../config/menu.php'  => config_path('menu.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/Views'   => resource_path('views/vendor/wmenu'),
        ], 'view');

        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/wmenu'),
        ], 'lang');

        $this->publishes([
            __DIR__ . '/../assets' => public_path('vendor/harimayco-menu'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/../migrations/2017_08_11_073824_create_menus_wp_table.php' => database_path('migrations/2017_08_11_073824_create_menus_wp_table.php'),
            __DIR__ . '/../migrations/2017_08_11_074006_create_menu_items_wp_table.php' => database_path('migrations/2017_08_11_074006_create_menu_items_wp_table.php'),
            __DIR__ . '/../migrations/2019_01_05_293551_add-role-id-to-menu-items-table.php' => database_path('migrations/2019_01_05_293551_add-role-id-to-menu-items-table.php'),
        ], 'migrations');
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->bind('harimayco-menu', function (): WMenu {
            return new WMenu();
        });

        $this->app->make(MenuController::class);
        $this->mergeConfigFrom(
            __DIR__ . '/../config/menu.php',
            'menu'
        );
    }
}
