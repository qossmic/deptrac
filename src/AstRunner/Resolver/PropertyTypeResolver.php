<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use Qossmic\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

class PropertyTypeResolver implements ClassDependencyResolver
{
    private TypeResolver $typeResolver;

    public function __construct(TypeResolver $typeResolver)
    {
        $this->typeResolver = $typeResolver;
    }

    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, TypeScope $typeScope): void
    {
        if (!$node instanceof Property) {
            return;
        }

        if (!$node->type) {
            return;
        }

        $types = $this->typeResolver->resolvePropertyType($node->type);
        foreach ($types as $type) {
            $classReferenceBuilder->variable($type, $node->getStartLine());
        }
    }
}
