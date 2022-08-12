<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser;

use Qossmic\Deptrac\Contract\Result\LegacyResult;
use Qossmic\Deptrac\Contract\Result\RuleInterface;
use function array_merge;
use function array_reduce;
use function array_values;

class LegacyDependencyLayersAnalyser
{
    public function __construct(private readonly DependencyLayersAnalyser $decorated)
    {
    }

    public function analyse(): LegacyResult
    {
        $ruleset = $this->decorated->process();

        /** @var RuleInterface[] $rules */
        $rules = array_reduce(
            $ruleset->all(),
            static fn (array $carry, array $rules): array => array_merge($carry, array_values($rules)),
            []
        );

        return new LegacyResult($rules, $ruleset->errors(), $ruleset->warnings());
    }
}
