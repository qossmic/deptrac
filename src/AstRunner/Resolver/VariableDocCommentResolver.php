<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;
use SensioLabs\Deptrac\AstRunner\PhpdocParser\ResolveNodeDocCommentTypes;

class VariableDocCommentResolver implements ClassDependencyResolver
{
    private $resolveNodeDocCommentTypes;

    public function __construct()
    {
        $this->resolveNodeDocCommentTypes = new ResolveNodeDocCommentTypes();
    }

    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, Context $context): void
    {
        if (!$node instanceof Node\Expr\Variable) {
            return;
        }

        ($this->resolveNodeDocCommentTypes)($classReferenceBuilder, $node, $context);
    }
}
