<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstDependency;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;

class ClassConstantResolver implements ClassDependencyResolver
{
    public function processNode(Node $node, AstFileReference $astFileReference, AstClassReference $astClassReference): void
    {
        if (!$node instanceof ClassConstFetch || !$node->class instanceof Node\Name) {
            return;
        }

        $astClassReference->addDependency(
            AstDependency::constFetch(
                $node->class->toString(),
                new FileOccurrence($astFileReference, $node->class->getLine())
            )
        );
    }
}
