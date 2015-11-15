<?php

namespace DependencyTracker\DependencyEmitter;

use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\Dependency;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstFileReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;

class UseDependencyEmitter implements DependencyEmitterInterface
{
    public function getName()
    {
        return 'UseDependencyEmitter';
    }

    public function supportsParser(AstParserInterface $astParser)
    {
        return $astParser instanceof NikicPhpParser;
    }

    private function getUseStatements(NikicPhpParser $astParser, AstFileReferenceInterface $fileReference) {

        $uses = [];

        foreach ($astParser->getAstByFile($fileReference) as $namespaceNode) {
            if (!$namespaceNode instanceof Namespace_ || !$namespaceNode->stmts) {
                continue;
            }

            foreach ($namespaceNode->stmts as $useNodes) {
                if (!$useNodes instanceof Use_) {
                    continue;
                }

                foreach ($useNodes->uses as $useNode) {
                    $uses[$useNode->name->toString()] = $useNode->name->getLine();
                }
            }
        }

        return $uses;
    }

    public function applyDependencies(AstParserInterface $astParser, AstMap $astMap, DependencyResult $dependencyResult)
    {
        /** @var $astParser NikicPhpParser */
        assert ($astParser instanceof NikicPhpParser === true);

        foreach ($astMap->getAstFileReferences() as $fileReference) {

            $uses = $this->getUseStatements($astParser, $fileReference);

            foreach ($fileReference->getAstClassReferences() as $astClassReference) {
                foreach ($uses as $use => $useLine) {
                    $dependencyResult->addDependency(
                        new Dependency(
                            $astClassReference->getClassName(), $useLine, $use, '?', '?'
                        )
                    );
                }
            }
        }
    }

}
