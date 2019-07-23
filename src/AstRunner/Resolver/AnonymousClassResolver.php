<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstDependency;

class AnonymousClassResolver implements ClassDependencyResolver
{
    public function processNode(Node $node, AstClassReference $astClassReference): void
    {
        if (!$node instanceof Node\Stmt\Class_ || null !== $node->name) {
            return;
        }

        if ($node->extends instanceof Node\Name) {
            $astClassReference->addDependency(
                AstDependency::anonymousClassExtends($node->extends->toString(), $node->extends->getLine())
            );
        }
        foreach ($node->implements as $implement) {
            $astClassReference->addDependency(
                AstDependency::anonymousClassImplements($implement->toString(), $implement->getLine())
            );
        }
    }
}
