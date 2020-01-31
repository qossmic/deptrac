<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

class ClassConstantResolver implements ClassDependencyResolver
{
    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, NameScope $nameScope): void
    {
        if (!$node instanceof ClassConstFetch || !$node->class instanceof Node\Name || $node->class->isSpecialClassName()) {
            return;
        }

        $classReferenceBuilder->constFetch($node->class->toString(), $node->class->getLine());
    }
}
