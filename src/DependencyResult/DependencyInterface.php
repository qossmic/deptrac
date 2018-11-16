<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\DependencyResult;

interface DependencyInterface
{
    public function getClassA(): string;

    public function getClassALine(): int;

    public function getClassB(): string;
}
