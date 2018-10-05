<?php
/**
 * Created by PhpStorm.
 * User: Jian Astrero
 * Date: 30/09/2018
 * Time: 4:11 PM
 */

namespace JianAstrero\JuggerAPI;

use function app_path;
use Illuminate\Support\ServiceProvider;
use JianAstrero\JuggerAPI\Console\Commands\JuggerAdminCommand;
use JianAstrero\JuggerAPI\Console\Commands\JuggerSeedCommand;

class JuggerAPIServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/routes.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'jugger-api');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            /**  frontend **/
            __DIR__.'/public' => public_path('jianastrero/jugger-api'),
            __DIR__ . '/resources/js' => resource_path('jianastrero/jugger-api/js'),
            __DIR__ . '/resources/sass' => resource_path('jianastrero/jugger-api/sass')
        ], 'jugger-api');
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                JuggerSeedCommand::class,
                JuggerAdminCommand::class
            ]);
        }
    }
}
