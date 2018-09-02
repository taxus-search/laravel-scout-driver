<?php

namespace TaxusSearch\LaravelScoutDriver;
use Laravel\Scout\EngineManager;
use TaxusSearch\LaravelScoutDriver\LaravelScoutDriverEngine;

use Illuminate\Support\ServiceProvider;

class LaravelScoutDriverProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
      resolve(EngineManager::class)->extend('taxus', function () {
        return new LaravelScoutDriverEngine;
      });
//            die('It is working');

      $this->publishes([
        __DIR__.'/../config/taxus.php' => $this->app['path.config'].DIRECTORY_SEPARATOR.'taxus.php',
      ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
