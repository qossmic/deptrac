<?php

namespace SensioLabs\Deptrac\DependencyEmitter;

use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstFileReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyResult\Dependency;

class BasicDependencyEmitter implements DependencyEmitterInterface
{
    public function getName(): string
    {
        return 'BasicDependencyEmitter';
    }

    public function supportsParser(AstParserInterface $astParser): bool
    {
        return $astParser instanceof NikicPhpParser;
    }

    /**
     * @param NikicPhpParser            $astParser
     * @param AstFileReferenceInterface $fileReference
     *
     * @return EmittedDependency[]
     */
    private function getUseStatements(NikicPhpParser $astParser, AstFileReferenceInterface $fileReference): array
    {
        $uses = [];
        $ast = $astParser->getAstByFile($fileReference);

        foreach ($ast as $namespaceNode) {
            if (!$namespaceNode instanceof Namespace_ || !$namespaceNode->stmts) {
                continue; // @codeCoverageIgnore
            }

            foreach ($namespaceNode->stmts as $useNodes) {
                if (!$useNodes instanceof Use_) {
                    continue; // @codeCoverageIgnore
                }

                foreach ($useNodes->uses as $useNode) {
                    $uses[] = new EmittedDependency(
                        $useNode->name->toString(),
                        $useNode->name->getLine(),
                        'use'
                    );
                }
            }
        }

        return $uses;
    }

    /**
     * @param NikicPhpParser             $astParser
     * @param AstClassReferenceInterface $classReference
     *
     * @return EmittedDependency[]
     */
    private function getInstanceOfStatements(NikicPhpParser $astParser, AstClassReferenceInterface $classReference): array
    {
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());

        /** @var Instanceof_[] $nodes */
        $nodes = $astParser->findNodesOfType((array) $ast, Instanceof_::class);

        foreach ($nodes as $instanceOf) {
            if (!$instanceOf->class instanceof Name) {
                continue;
            }

            $buffer[] = new EmittedDependency(
                $instanceOf->class->toString(),
                $instanceOf->getLine(),
                'instanceof'
            );
        }

        return $buffer;
    }

    /**
     * @param NikicPhpParser             $astParser
     * @param AstClassReferenceInterface $classReference
     *
     * @return EmittedDependency[]
     */
    private function getParamStatements(NikicPhpParser $astParser, AstClassReferenceInterface $classReference): array
    {
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());

        /** @var Param[] $nodes */
        $nodes = $astParser->findNodesOfType((array) $ast, Param::class);

        foreach ($nodes as $node) {
            if (!$node->type instanceof Name) {
                continue;
            }

            $buffer[] = new EmittedDependency(
                $node->type->toString(),
                $node->type->getLine(),
                'parameter'
            );
        }

        return $buffer;
    }

    /**
     * @param NikicPhpParser             $astParser
     * @param AstClassReferenceInterface $classReference
     *
     * @return EmittedDependency[]
     */
    private function getReturnTypes(NikicPhpParser $astParser, AstClassReferenceInterface $classReference): array
    {
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());

        /** @var ClassMethod[]|Closure[] $canHaveReturnTypes */
        $canHaveReturnTypes = array_merge(
            $astParser->findNodesOfType((array) $ast, ClassMethod::class),
            $astParser->findNodesOfType((array) $ast, Closure::class)
        );

        foreach ($canHaveReturnTypes as $node) {
            if (!$node->returnType instanceof Name) {
                continue;
            }
            $buffer[] = new EmittedDependency(
                $node->returnType->toString(),
                $node->returnType->getLine(),
                'returntype'
            );
        }

        foreach ($canHaveReturnTypes as $node) {
            if (!$node->returnType instanceof NullableType) {
                continue;
            }

            if ($node->returnType->type instanceof Name) {
                $buffer[] = new EmittedDependency(
                    $node->returnType->type->toString(),
                    $node->returnType->getLine(),
                    'returntype'
                );
                continue;
            }

            $buffer[] = new EmittedDependency(
                $node->returnType->type,
                $node->returnType->getLine(),
                'returntype'
            );
        }

        return $buffer;
    }

    /**
     * @param NikicPhpParser             $astParser
     * @param AstClassReferenceInterface $classReference
     *
     * @return EmittedDependency[]
     */
    private function getNewStatements(NikicPhpParser $astParser, AstClassReferenceInterface $classReference): array
    {
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());

        /** @var New_[] $nodes */
        $nodes = $astParser->findNodesOfType((array) $ast, New_::class);

        foreach ($nodes as $node) {
            if (!$node->class instanceof Name) {
                continue;
            }

            $buffer[] = new EmittedDependency(
                $node->class->toString(),
                $node->class->getLine(),
                'new'
            );
        }

        return $buffer;
    }

    /**
     * @param NikicPhpParser             $astParser
     * @param AstClassReferenceInterface $classReference
     *
     * @return EmittedDependency[]
     */
    private function getStaticPropertiesAccess(NikicPhpParser $astParser, AstClassReferenceInterface $classReference): array
    {
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());

        /** @var StaticPropertyFetch[] $nodes */
        $nodes = $astParser->findNodesOfType((array) $ast, StaticPropertyFetch::class);

        foreach ($nodes as $node) {
            if (!$node->class instanceof Name) {
                continue;
            }

            $buffer[] = new EmittedDependency(
                $node->class->toString(),
                $node->class->getLine(),
                'static_property'
            );
        }

        return $buffer;
    }

    /**
     * @param NikicPhpParser             $astParser
     * @param AstClassReferenceInterface $classReference
     *
     * @return EmittedDependency[]
     */
    private function getStaticMethodCalls(NikicPhpParser $astParser, AstClassReferenceInterface $classReference): array
    {
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());

        /** @var StaticCall[] $nodes */
        $nodes = $astParser->findNodesOfType((array) $ast, StaticCall::class);

        foreach ($nodes as $node) {
            if (!$node->class instanceof Name) {
                continue; // @codeCoverageIgnore
            }

            $buffer[] = new EmittedDependency(
                $node->class->toString(),
                $node->class->getLine(),
                'static_method'
            );
        }

        return $buffer;
    }

    public function applyDependencies(
        AstParserInterface $astParser,
        AstMap $astMap,
        Result $dependencyResult
    ) {
        /* @var $astParser NikicPhpParser */
        assert(true === $astParser instanceof NikicPhpParser);

        foreach ($astMap->getAstFileReferences() as $fileReference) {
            $uses = $this->getUseStatements($astParser, $fileReference);

            foreach ($fileReference->getAstClassReferences() as $astClassReference) {
                /** @var EmittedDependency[] $uses */
                $uses = array_merge(
                    $uses,
                    $this->getInstanceOfStatements($astParser, $astClassReference),
                    $this->getParamStatements($astParser, $astClassReference),
                    $this->getNewStatements($astParser, $astClassReference),
                    $this->getStaticPropertiesAccess($astParser, $astClassReference),
                    $this->getStaticMethodCalls($astParser, $astClassReference),
                    $this->getReturnTypes($astParser, $astClassReference)
                );

                foreach ($uses as $emittedDependency) {
                    $dependencyResult->addDependency(
                        new Dependency(
                            $astClassReference->getClassName(),
                            $emittedDependency->getLine(),
                            $emittedDependency->getClass()
                        )
                    );
                }
            }
        }
    }
}
