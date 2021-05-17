<?php
/*
 *
  ____                 _ _     _____           _                       _
 |  _ \               | | |   |_   _|         | |                     (_)
 | |_) | ___ _ __   __| | |_    | |  _ __   __| | ___  _ __   ___  ___ _  __ _
 |  _ < / _ \ '_ \ / _` | __|   | | | '_ \ / _` |/ _ \| '_ \ / _ \/ __| |/ _` |
 | |_) |  __/ | | | (_| | |_   _| |_| | | | (_| | (_) | | | |  __/\__ \ | (_| |
 |____/ \___|_| |_|\__,_|\__| |_____|_| |_|\__,_|\___/|_| |_|\___||___/_|\__,_|

 Last Update 16 Aug 2020
 */

namespace Bendt\Zip;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class BendtZipServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        Schema::defaultStringLength(191);

        $this->publishes([
            __DIR__.'/config/bendt-zip.php' => config_path('bendt-zip.php'),
        ], 'config');

        if(config('bendt-zip.migration', true)) {
            $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        }

        //Require Routes if not disabled
        if(config('bendt-zip.api_route', true)) {
            require __DIR__ . '/routes/api.php';
        }
    }
}
