<?php

namespace Luminee\Chariot;

use Illuminate\Support\ServiceProvider;

class ChariotServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $config = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
    protected $src = __DIR__ . DIRECTORY_SEPARATOR;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([$this->config . 'chariot.php' => config_path('chariot.php')]);
        $this->publishes([$this->src . 'chariot' => base_path('chariot')]);
        $this->publishes([$this->src . '.conn.conf.php.example' => base_path('.conn.conf.php.example')]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (file_exists($this->config . 'chariot.php'))
            $this->mergeConfigFrom($this->config . 'chariot.php', 'chariot');
    }

}