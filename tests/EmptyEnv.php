<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac;

use Qossmic\Deptrac\Env;

final class EmptyEnv extends Env
{
    public function get(string $envName)
    {
        return false;
    }
}
