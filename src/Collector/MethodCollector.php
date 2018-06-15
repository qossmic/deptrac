<?php

namespace SensioLabs\Deptrac\Collector;

use PhpParser\Node\Stmt\ClassMethod;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;

class MethodCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'method';
    }

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $classReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        AstParserInterface $astParser
    ): bool {
        if (!$astParser instanceof NikicPhpParser) {
            return false;
        }

        $ast = $astParser->getAstForClassname($classReference->getClassName());

        /** @var ClassMethod[] $classMethods */
        $classMethods = $astParser->findNodesOfType((array) $ast, ClassMethod::class);
        $pattern = $this->getPattern($configuration);

        foreach ($classMethods as $classMethod) {
            if (1 === preg_match($pattern, $classMethod->name)) {
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
