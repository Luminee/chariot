<?php

namespace Luminee\Chariot\Console;

use App\Console\Kernel as KernelApp;
use Luminee\Chariot\Console\Concerns\Kernel;

class KernelArtisan extends KernelApp
{
    use Kernel;
}
