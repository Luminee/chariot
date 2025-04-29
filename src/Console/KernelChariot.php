<?php

namespace Luminee\Chariot\Console;

use Illuminate\Foundation\Console\Kernel as KernelFoundation;
use Luminee\Chariot\Console\Concerns\Kernel;

class KernelChariot extends KernelFoundation
{
    use Kernel;
}
