<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\PhpParser;

use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;
use SensioLabs\Deptrac\AstRunner\Resolver\AnonymousClassResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\CatchStmtResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassConstantResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\ClassDependencyResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\ClosureResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\InstanceofResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\NewExprResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\StaticCallResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\StaticPropertyFetchResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\TypeResolver;
use SensioLabs\Deptrac\AstRunner\Resolver\VariableDocCommentResolver;

final class ResolveClassMethodDependencyAwareNodeTypes
{
    /**
     * @var ClassDependencyResolver[]
     */
    private $classDependencyResolvers;

    public function __construct(TypeResolver $typeResolver)
    {
        $this->classDependencyResolvers = [
            new AnonymousClassResolver(),
            new CatchStmtResolver($typeResolver),
            new ClassConstantResolver(),
            new ClosureResolver($typeResolver),
            new InstanceofResolver($typeResolver),
            new NewExprResolver($typeResolver),
            new StaticCallResolver($typeResolver),
            new StaticPropertyFetchResolver($typeResolver),
            new VariableDocCommentResolver(),
        ];
    }

    public function __invoke(
        ClassReferenceBuilder $classReferenceBuilder,
        ClassMethod $classMethod,
        Context $context
    ): void {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(
            new class($classReferenceBuilder, $context, ...$this->classDependencyResolvers) extends NodeVisitorAbstract {
                private $classReferenceBuilder;
                private $context;
                private $classDependencyResolvers;

                public function __construct(
                    ClassReferenceBuilder $classReferenceBuilder,
                    Context $context,
                    ClassDependencyResolver ...$classDependencyResolvers
                ) {
                    $this->classReferenceBuilder = $classReferenceBuilder;
                    $this->context = $context;
                    $this->classDependencyResolvers = $classDependencyResolvers;
                }

                public function leaveNode(Node $node)
                {
                    foreach ($this->classDependencyResolvers as $resolver) {
                        $resolver->processNode($node, $this->classReferenceBuilder, $this->context);
                    }

                    return null;
                }
            }
        );
        $nodeTraverser->traverse($classMethod->stmts);
    }
}
