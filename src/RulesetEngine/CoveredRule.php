<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\RulesetEngine;

/**
 * @psalm-immutable
 */
interface CoveredRule extends Rule
{
    public function getDependantLayerName(): string;

    public function getDependeeLayerName(): string;
}
