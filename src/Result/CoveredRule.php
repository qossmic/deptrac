<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Result;

/**
 * @psalm-immutable
 */
interface CoveredRule extends Rule
{
    public function getDependerLayer(): string;

    public function getDependentLayer(): string;
}
