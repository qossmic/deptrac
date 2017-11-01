<?php

namespace SensioLabs\Deptrac\Collector;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\CollectorFactory;

class ImplementsCollector
{
    public function getType()
    {
        return 'implements';
    }

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $classReference,
        AstMap $astMap,
        CollectorFactory $collectorFactory,
        AstParserInterface $astParser
    ) {
        $requiredInterface = $this->getInterfaceName($configuration);

        $interfaces = [];
        $this->getImplementedInterfaces($classReference->getClassName(), $astParser, $interfaces);

        return \in_array($requiredInterface, $interfaces, true);
    }

    private function getInterfaceName(array $configuration)
    {
        if (!isset($configuration['implements'])) {
            throw new \LogicException('ImplementsCollector needs the interface name.');
        }

        return $configuration['implements'];
    }

    /**
     * Recursively extract interfaces from AST tree
     */
    private function getImplementedInterfaces($fqn, AstParserInterface $astParser, array &$interfaces)
    {
        assert($astParser instanceof NikicPhpParser);
        assert(is_string($fqn));

        $node = $astParser->getAstForClassname($fqn);
        if ($node instanceof Interface_) {
            $interfaces[] = (string) $fqn;

            if (is_array($node->extends)) {
                foreach ($node->extends as $extend) {
                    assert($extend instanceof Name);
                    assert($extend->isFullyQualified());
                    $this->getImplementedInterfaces($extend->toString(), $astParser, $interfaces);
                }
            }
            return;
        }

        if (!$node instanceof Class_) {
            return;
        }

        if (is_array($node->implements)) {
            foreach ($node->implements as $implement) {
                assert($implement instanceof Name);
                assert($implement->isFullyQualified());
                $this->getImplementedInterfaces($implement->toString(), $astParser, $interfaces);
            }
        }

        // Trait cannot add any formal interface in PHP class
    }
}
