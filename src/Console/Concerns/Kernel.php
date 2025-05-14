<?php

namespace Luminee\Chariot\Console\Concerns;

use Luminee\Chariot\Console\Chariot;

trait Kernel
{
    /**
     * The Artisan application instance.
     *
     * @var Chariot
     */
    protected $chariot;

    /**
     * @return Chariot
     */
    protected function getArtisan()
    {
        return $this->getChariot();
    }

    /**
     * Get the Artisan application instance.
     *
     * @return Chariot
     */
    protected function getChariot()
    {
        if (is_null($this->chariot)) {
            return $this->chariot = (new Chariot($this->app, $this->events, $this->app->version()))
                ->loadCommands()->resolveCommands($this->commands);
        }

        return $this->chariot;
    }
}