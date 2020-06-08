<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

class CatchStmtResolver implements ClassDependencyResolver
{
    private $typeResolver;

    public function __construct(TypeResolver $typeResolver)
    {
        $this->typeResolver = $typeResolver;
    }

    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, Context $context): void
    {
        if ($node instanceof Node\Stmt\Catch_) {
            foreach ($this->typeResolver->resolvePHPParserTypes($context, ...$node->types) as $classLikeName) {
                $classReferenceBuilder->catchStmt($classLikeName, $node->getLine());
            }
        }
    }
}
