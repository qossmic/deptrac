<?php

namespace Tests\Qossmic\Deptrac\Console;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Collector\CollectorInterface;
use Qossmic\Deptrac\Collector\Registry;

class DummyCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'dummy';
    }

    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        array $resolutionTable = []
    ): bool {
        return true;
    }

    public function resolvable(array $configuration, Registry $collectorRegistry, array $resolutionTable): bool
    {
        return true;
    }
}
