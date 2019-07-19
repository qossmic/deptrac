<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstDependency;

class ClassConstantResolver implements ClassDependencyResolver
{
    public function processNode(Node $node, AstClassReference $astClassReference): void
    {
        if (!$node instanceof ClassConstFetch || !$node->class instanceof Node\Name) {
            return;
        }

        $astClassReference->addDependency(
            AstDependency::constFetch($node->class->toString(), $node->class->getLine())
        );
    }
}
