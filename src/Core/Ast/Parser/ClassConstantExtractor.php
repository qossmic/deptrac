<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;

class ClassConstantExtractor implements ReferenceExtractorInterface
{
    public function processNode(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if (!$node instanceof ClassConstFetch || !$node->class instanceof Name || $node->class->isSpecialClassName()) {
            return;
        }

        $referenceBuilder->constFetch($node->class->toCodeString(), $node->class->getLine());
    }
}
