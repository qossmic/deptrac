<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac;

use SensioLabs\Deptrac\Env;

final class EmptyEnv extends Env
{
    public function get(string $envName)
    {
        return false;
    }
}
