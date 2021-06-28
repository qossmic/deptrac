<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use Qossmic\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

class AnonymousClassResolver implements ClassDependencyResolver
{
    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, TypeScope $typeScope): void
    {
        if (!$node instanceof Class_ || null !== $node->name) {
            return;
        }

        if ($node->extends instanceof Name) {
            $classReferenceBuilder->anonymousClassExtends($node->extends->toCodeString(), $node->extends->getLine());
        }
        foreach ($node->implements as $implement) {
            $classReferenceBuilder->anonymousClassImplements($implement->toCodeString(), $implement->getLine());
        }
    }
}
