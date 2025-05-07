<?php

namespace Luminee\Chariot\Console;

use Illuminate\Console\Command as IlluminateCommand;
use ReflectionClass;

abstract class Command extends IlluminateCommand
{
    /**
     * @var string
     */
    protected $scripts_dir = '';

    /**
     * @var string
     */
    protected $directory_separator = '';

    public function __construct()
    {
        $this->scripts_dir = realpath(config('chariot.scripts_dir'));

        $this->directory_separator = config('chariot.signature.directory_separator');

        parent::__construct();
    }

    /**
     * Configure the console command using a fluent definition.
     *
     * @return void
     */
    protected function configureUsingFluentDefinition()
    {
        $this->getChariotSignature();

        parent::configureUsingFluentDefinition();
    }

    protected function getChariotSignature()
    {
        $separator = DIRECTORY_SEPARATOR;

        $ref = new ReflectionClass($this);
        if (strpos($ref->getFileName(), $this->scripts_dir) === false) {
            return;
        }
        $file = str_replace($this->scripts_dir . $separator, '', $ref->getFileName());

        $x = explode($separator, $file);
        array_pop($x);
        if (count($x) <= 0) {
            return;
        }
        $directory = strtolower(implode('.', $x));

        $this->signature = $directory . $this->directory_separator . $this->signature;
    }
}
