<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;

class ClassNameCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'className';
    }

    public function satisfy(
        array $configuration,
        AstClassReference $astClassReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        AstParserInterface $astParser
    ): bool {
        return 1 === preg_match($this->getPattern($configuration), $astClassReference->getClassName());
    }

    private function getPattern(array $configuration): string
    {
        if (!isset($configuration['regex'])) {
            throw new \LogicException('ClassNameCollector needs the regex configuration.');
        }

        return '/'.$configuration['regex'].'/i';
    }
}
