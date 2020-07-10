<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

class Env
{
    /**
     * @return string|false Environment variable value or false if the variable does not exist
     */
    public function get(string $name)
    {
        return getenv($name);
    }
}
