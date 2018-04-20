<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\CollectorFactory;
use PhpParser\Node\Stmt\ClassMethod;

class MethodCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'method';
    }

    private function getMethodNameRegexByConfiguration(array $configuration)
    {
        if (!isset($configuration['name'])) {
            throw new \LogicException('MethodCollector needs the name configuration.');
        }

        return $configuration['name'];
    }

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $classReference,
        AstMap $astMap,
        CollectorFactory $collectorFactory,
        AstParserInterface $astParser
    ): bool {
        if (!$astParser instanceof NikicPhpParser) {
            return false;
        }

        $ast = $astParser->getAstForClassname($classReference->getClassName());

        /** @var ClassMethod[] $classMethods */
        $classMethods = $astParser->findNodesOfType((array) $ast, ClassMethod::class);

        foreach ($classMethods as $classMethod) {
            if (1 === preg_match(
                '/'.$this->getMethodNameRegexByConfiguration($configuration).'/i',
                $classMethod->name
            )) {
                return true;
            }
        }

        return false;
    }
}
