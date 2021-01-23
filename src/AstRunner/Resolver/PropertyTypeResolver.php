<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

class PropertyTypeResolver implements ClassDependencyResolver
{
    /** @var TypeResolver */
    private $typeResolver;

    public function __construct(TypeResolver $typeResolver)
    {
        $this->typeResolver = $typeResolver;
    }

    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, TypeScope $typeScope): void
    {
        if (!$node instanceof Node\Stmt\Property) {
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
