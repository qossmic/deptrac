<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

class StaticPropertyFetchResolver implements ClassDependencyResolver
{
    private $typeResolver;

    public function __construct(TypeResolver $typeResolver)
    {
        $this->typeResolver = $typeResolver;
    }

    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, Context $context): void
    {
        if ($node instanceof Node\Expr\StaticPropertyFetch && $node->class instanceof Node\Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($context, $node->class) as $classLikeName) {
                $classReferenceBuilder->staticProperty($classLikeName, $node->class->getLine());
            }
        }
    }
}
