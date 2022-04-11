<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

interface LoaderInterface
{
    public function load(string $file): Configuration;
}
