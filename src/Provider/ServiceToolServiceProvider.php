<?php

namespace Ices\ServiceTool\Provider;

use Illuminate\Support\ServiceProvider;
use Ices\ServiceTool\Commands\ServiceDestroyCommand;
use Ices\ServiceTool\Commands\ServiceMakeCommand;

class ServiceToolServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/command.php', 'command'
        );
    }
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerCommands();
    }

    function registerCommands() {
        $this->commands([
            ServiceDestroyCommand::class,
            ServiceMakeCommand::class
        ]);
    }
}