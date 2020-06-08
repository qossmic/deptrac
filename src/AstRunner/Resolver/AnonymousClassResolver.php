<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

class AnonymousClassResolver implements ClassDependencyResolver
{
    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, Context $context): void
    {
        if (!$node instanceof Node\Stmt\Class_ || null !== $node->name) {
            return;
        }

        if ($node->extends instanceof Node\Name) {
            $classReferenceBuilder->anonymousClassExtends($node->extends->toCodeString(), $node->extends->getLine());
        }
        foreach ($node->implements as $implement) {
            $classReferenceBuilder->anonymousClassImplements($implement->toCodeString(), $implement->getLine());
        }
    }
}
