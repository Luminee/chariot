<?php

namespace Luminee\Chariot;

use Illuminate\Support\ServiceProvider;

class ChariotServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $config = __DIR__ . '/../config';

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([$this->config . 'chariot.php' => config_path('chariot.php')]);
        $this->publishes([__DIR__ . '/chariot' => base_path('chariot')]);
        $this->publishes([__DIR__ . '/.conn.conf.php.example' => base_path('.conn.conf.php.example')]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->config = realpath($this->config) . DIRECTORY_SEPARATOR;
        if (file_exists($this->config . 'chariot.php')) {
            $this->mergeConfigFrom($this->config . 'chariot.php', 'chariot');
        }
    }

}