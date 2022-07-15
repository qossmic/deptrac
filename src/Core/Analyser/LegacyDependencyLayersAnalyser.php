<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser;

use Qossmic\Deptrac\Contract\Result\LegacyResult;
use Qossmic\Deptrac\Contract\Result\Rule;
use function array_merge;
use function array_reduce;
use function array_values;

class LegacyDependencyLayersAnalyser
{
    private DependencyLayersAnalyser $decorated;

    public function __construct(DependencyLayersAnalyser $decorated)
    {
        $this->decorated = $decorated;
    }

    public function analyse(): LegacyResult
    {
        $ruleset = $this->decorated->process();

        /** @var Rule[] $rules */
        $rules = array_reduce(
            $ruleset->all(),
            static fn (array $carry, array $rules): array => array_merge($carry, array_values($rules)),
            []
        );

        return new LegacyResult($rules, $ruleset->errors(), $ruleset->warnings());
    }
}
