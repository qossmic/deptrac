<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Result;

/**
 * @psalm-immutable
 */
interface CoveredRuleInterface extends RuleInterface
{
    public function getDependerLayer(): string;

    public function getDependentLayer(): string;
}
