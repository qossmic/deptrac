<?php

namespace DependencyTracker\DependencyEmitter;

use DependencyTracker\DependencyResult;
use DependencyTracker\DependencyResult\Dependency;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstFileReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;

class BasicDependencyEmitter implements DependencyEmitterInterface
{
    public function getName()
    {
        return 'BasicDependencyEmitter';
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

    private function getInstanceOfStatements(NikicPhpParser $astParser, AstClassReferenceInterface $classReference) {
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());
        foreach ($astParser->findNodesOfType($ast, Instanceof_::class) as $instanceOf) {
            /** @var $instanceOf Instanceof_ */
            if (!$instanceOf->class instanceOf Name) {
                continue;
            }

            $buffer[$instanceOf->class->toString()] = $instanceOf->getLine();
        }

        return $buffer;
    }

    private function getParamStatements(NikicPhpParser $astParser, AstClassReferenceInterface $classReference) {
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());
        foreach ($astParser->findNodesOfType($ast, Param::class) as $node) {
            /** @var $node Param */
            if (!$node->type instanceOf Name) {
                continue;
            }

            $buffer[$node->type->toString()] = $node->type->getLine();
        }

        return $buffer;
    }

    private function getNewStatements(NikicPhpParser $astParser, AstClassReferenceInterface $classReference) {
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());
        foreach ($astParser->findNodesOfType($ast, New_::class) as $node) {
            /** @var $node New_ */
            if (!$node->class instanceOf Name) {
                continue;
            }

            $buffer[$node->class->toString()] = $node->class->getLine();
        }

        return $buffer;
    }


    public function applyDependencies(
        AstParserInterface $astParser,
        AstMap $astMap,
        DependencyResult $dependencyResult
    )
    {
        /** @var $astParser NikicPhpParser */
        assert ($astParser instanceof NikicPhpParser === true);

        foreach ($astMap->getAstFileReferences() as $fileReference) {

            $uses = $this->getUseStatements($astParser, $fileReference);

            foreach ($fileReference->getAstClassReferences() as $astClassReference) {

                $uses = array_merge(
                    $uses,
                    $this->getInstanceOfStatements($astParser, $astClassReference),
                    $this->getParamStatements($astParser, $astClassReference),
                    $this->getNewStatements($astParser, $astClassReference)
                );

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
