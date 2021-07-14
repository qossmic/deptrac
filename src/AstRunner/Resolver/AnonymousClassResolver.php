<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use Qossmic\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;
use Qossmic\Deptrac\AstRunner\AstMap\TokenReferenceBuilder;

class AnonymousClassResolver implements DependencyResolver
{
    public function processNode(Node $node, TokenReferenceBuilder $tokenReferenceBuilder, TypeScope $typeScope): void
    {
        if (!$node instanceof Class_ || null !== $node->name) {
            return;
        }

        if ($node->extends instanceof Name) {
            $tokenReferenceBuilder->anonymousClassExtends($node->extends->toCodeString(), $node->extends->getLine());
        }
        foreach ($node->implements as $implement) {
            $tokenReferenceBuilder->anonymousClassImplements($implement->toCodeString(), $implement->getLine());
        }
    }
}
