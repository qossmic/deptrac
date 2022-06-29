<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\Console;

use Qossmic\Deptrac\Supportive\Console\Env;

final class EmptyEnv extends Env
{
    public function get(string $envName)
    {
        return false;
    }
}
