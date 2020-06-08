<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

class ClassConstantResolver implements ClassDependencyResolver
{
    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, Context $context): void
    {
        if (!$node instanceof ClassConstFetch || !$node->class instanceof Node\Name || $node->class->isSpecialClassName()) {
            return;
        }

        $classReferenceBuilder->constFetch($node->class->toCodeString(), $node->class->getLine());
    }
}
