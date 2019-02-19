<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Collector;

use PhpParser\Node\Stmt\ClassMethod;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;

class MethodCollector implements CollectorInterface
{
    private $nikicPhpParser;

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
        AstClassReference $classReference,
        AstMap $astMap,
        Registry $collectorRegistry
    ): bool {
        $ast = $this->nikicPhpParser->getAstForClassname($classReference->getClassName());

        /** @var ClassMethod[] $classMethods */
        $classMethods = $this->nikicPhpParser->findNodesOfType((array) $ast, ClassMethod::class);
        $pattern = $this->getPattern($configuration);

        foreach ($classMethods as $classMethod) {
            if (1 === preg_match($pattern, (string) $classMethod->name)) {
                return true;
            }
        }

        return false;
    }

    private function getPattern(array $configuration): string
    {
        if (!isset($configuration['name'])) {
            throw new \LogicException('MethodCollector needs the name configuration.');
        }

        return '/'.$configuration['name'].'/i';
    }
}
