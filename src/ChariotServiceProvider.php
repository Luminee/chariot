<?php

namespace Luminee\Chariot;

use Luminee\Chariot\Commands\MakeScriptCommand;
use Luminee\Foundry\Abstracts\ServiceProvider;

class ChariotServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();

        $this->publishes([__DIR__ . '/chariot' => base_path('chariot')]);

        $this->commands([
            MakeScriptCommand::class
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerVendor(__DIR__ . '/../', 'chariot');

        $this->mergeConfig();
    }
}