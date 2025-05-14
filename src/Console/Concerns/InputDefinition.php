<?php

namespace Luminee\Chariot\Console\Concerns;

use ReflectionClass;
use Symfony\Component\Console\Input\InputOption;

trait InputDefinition
{
    protected function addOptions()
    {
        $this->getDefinition()->addOption(
            new InputOption('--run', '-r', InputOption::VALUE_NONE, 'Run the command')
        );
    }

    protected function getChariotSignature()
    {
        $ref = new ReflectionClass($this);
        if (strpos($ref->getFileName(), $this->scripts_dir) === false) {
            return;
        }

        $file = str_replace($this->scripts_dir . DIRECTORY_SEPARATOR, '', $ref->getFileName());

        $x = explode(DIRECTORY_SEPARATOR, $file);
        array_pop($x);
        if (count($x) <= 0) {
            return;
        }

        $directory = strtolower(implode('.', $x));

        $this->signature = $directory . $this->directory_separator . $this->signature;
    }
}
