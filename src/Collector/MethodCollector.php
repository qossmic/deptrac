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
    public function getType()
    {
        return 'method';
    }

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $classReference,
        AstMap $astMap,
        CollectorFactory $collectorFactory,
        AstParserInterface $astParser
    ) {
        if (!$astParser instanceof NikicPhpParser) {
            return false;
        }

        $ast = $astParser->getAstForClassname($classReference->getClassName());

        /** @var $classMethods ClassMethod[] */
        $classMethods = $astParser->findNodesOfType($ast, ClassMethod::class);

        foreach ($classMethods as $classMethod) {
            if (preg_match(
                $this->getMethodNameConfigurationRegex($configuration),
                $classMethod->name,
                $collectorFactory
            )) {
                return true;
            }
        }

        return false;
    }

    private function getMethodNameConfigurationRegex(array $configuration)
    {
        if (!isset($configuration['name'])) {
            throw new \LogicException('MethodCollector needs the name configuration.');
        }

        return sprintf('/%s/i', $configuration['name']);
    }
}
