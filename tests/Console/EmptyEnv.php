<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Console;

use Qossmic\Deptrac\Console\Env;

final class EmptyEnv extends Env
{
    public function get(string $envName)
    {
        return false;
    }
}
