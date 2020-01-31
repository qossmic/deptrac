<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstDependency;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;

class ClassConstantResolver implements ClassDependencyResolver
{
    public function processNode(Node $node, AstFileReference $astFileReference, AstClassReference $astClassReference, NameScope $nameScope): void
    {
        if (!$node instanceof ClassConstFetch || !$node->class instanceof Node\Name || $node->class->isSpecialClassName()) {
            return;
        }

        $astClassReference->addDependency(
            AstDependency::constFetch(
                ClassLikeName::fromString($node->class->toString()),
                new FileOccurrence($astFileReference, $node->class->getLine())
            )
        );
    }
}
