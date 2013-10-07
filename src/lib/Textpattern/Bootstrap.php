<?php

namespace Textpattern;
use Composer\Autoload\ClassLoader;
use Textpattern\Fluxbb\Filter;
use Textpattern\Fluxbb\Sfs;
use Textpattern\Fluxbb\Trap;
use Textpattern\Fluxbb\Tasks;

/**
 * Bootstrap.
 */

class Bootstrap
{
    /**
     * Constructor.
     */

    public function __construct()
    {
        $loader = new ClassLoader();
        $loader->add('Textpattern\\Fluxbb', dirname(__DIR__));
        $loader->register();
    }
}

new Bootstrap();
new Filter();
new Sfs();

if (defined('\TEXTPATTERN_TRAP_URL'))
{
    new Trap(\TEXTPATTERN_TRAP_URL);
}

if (defined('\TEXTPATTERN_TASKS_KEY'))
{
    new Tasks(\TEXTPATTERN_TASKS_KEY);
}
