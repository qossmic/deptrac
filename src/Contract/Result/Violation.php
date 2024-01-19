<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Contract\Result;

use Qossmic\Deptrac\Contract\Analyser\ViolationCreatingInterface;
use Qossmic\Deptrac\Contract\Dependency\DependencyInterface;
/**
 * @psalm-immutable
 *
 * Represents a dependency that is NOT allowed to exist given the defined rules
 */
final class Violation implements \Qossmic\Deptrac\Contract\Result\CoveredRuleInterface
{
    public function __construct(private readonly DependencyInterface $dependency, private readonly string $dependerLayer, private readonly string $dependentLayer, private readonly ViolationCreatingInterface $violationCreatingRule)
    {
    }
    public function getDependency() : DependencyInterface
    {
        return $this->dependency;
    }
    public function getDependerLayer() : string
    {
        return $this->dependerLayer;
    }
    public function getDependentLayer() : string
    {
        return $this->dependentLayer;
    }
    public function ruleName() : string
    {
        return $this->violationCreatingRule->ruleName();
    }
    public function ruleDescription() : string
    {
        return $this->violationCreatingRule->ruleDescription();
    }
}
