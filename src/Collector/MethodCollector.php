<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use LogicException;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;

class MethodCollector extends RegexCollector implements CollectorInterface
{
    private NikicPhpParser $nikicPhpParser;

    public function __construct(NikicPhpParser $nikicPhpParser)
    {
        $this->nikicPhpParser = $nikicPhpParser;
    }

    public function getType(): string
    {
        return 'method';
    }

    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        array $resolutionTable = []
    ): bool {
        if (!$astTokenReference instanceof AstClassReference) {
            return false;
        }

        $pattern = $this->getValidatedPattern($configuration);

        $classLike = $this->nikicPhpParser->getAstForClassReference($astTokenReference);

        if (null === $classLike) {
            return false;
        }

        foreach ($classLike->getMethods() as $classMethod) {
            if (1 === preg_match($pattern, (string) $classMethod->name)) {
                return true;
            }
        }

        return false;
    }

    protected function getPattern(array $configuration): string
    {
        if (!isset($configuration['name']) || !is_string($configuration['name'])) {
            throw new LogicException('MethodCollector needs the name configuration.');
        }

        return '/'.$configuration['name'].'/i';
    }
}
