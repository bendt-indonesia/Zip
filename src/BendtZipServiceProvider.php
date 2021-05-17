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

use Bendt\Option\Classes\OptionManager;
use Bendt\Option\Facades\Option as OptionFacades;

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
            __DIR__.'/config/bendt-option.php' => config_path('bendt-option.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/Export/QueryFilter.php' => app_path('Filters/QueryFilter.php'),
        ], 'filters');

        $this->publishes([
            __DIR__.'/Export/IEnum.php' => app_path('Interfaces/IEnum.php'),
            __DIR__.'/Export/EnumClass.php' => app_path('Enums/EnumClass.php'),
            __DIR__.'/Export/GenderType.php' => app_path('Enums/GenderType.php'),
        ], 'enum');

        $this->publishes([
            __DIR__.'/Export/Traits/BelongsToCreatedByTrait.php' => app_path('Traits/BelongsToCreatedByTrait.php'),
            __DIR__.'/Export/Traits/BelongsToDeletedByTrait.php' => app_path('Traits/BelongsToDeletedByTrait.php'),
            __DIR__.'/Export/Traits/BelongsToUpdatedByTrait.php' => app_path('Traits/BelongsToUpdatedByTrait.php'),
            __DIR__.'/Export/Traits/RelationshipTrait.php' => app_path('Traits/RelationshipTrait.php'),
            __DIR__.'/Export/Traits/ScopeActiveTrait.php' => app_path('Traits/ScopeActiveTrait.php'),
            __DIR__.'/Export/Traits/ScopeAscTrait.php' => app_path('Traits/ScopeAscTrait.php'),
            __DIR__.'/Export/Traits/ScopeDescTrait.php' => app_path('Traits/ScopeDescTrait.php'),
            __DIR__.'/Export/Traits/ScopeFilter.php' => app_path('Traits/ScopeFilter.php'),
        ], 'traits');

        if(config('bendt-option.migration', true)) {
            $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        }

        //Load helper
        require __DIR__ . '/helper.php';

        //Require Routes if not disabled
        if(config('bendt-option.api_route', true)) {
            require __DIR__ . '/routes/api.php';
        }
    }
}
