<?php

namespace Luminee\Chariot\Console;

use Illuminate\Console\Command as IlluminateCommand;
use Luminee\Chariot\Console\Concerns\InputDefinition;

abstract class Command extends IlluminateCommand
{
    use InputDefinition;

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

        $this->addOptions();
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
}
