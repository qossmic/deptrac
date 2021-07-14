<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\Resolver;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use Qossmic\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;
use Qossmic\Deptrac\AstRunner\AstMap\TokenReferenceBuilder;

class PropertyTypeResolver implements DependencyResolver
{
    private TypeResolver $typeResolver;

    public function __construct(TypeResolver $typeResolver)
    {
        $this->typeResolver = $typeResolver;
    }

    public function processNode(Node $node, TokenReferenceBuilder $tokenReferenceBuilder, TypeScope $typeScope): void
    {
        //TODO: What about anonymous class properties inside function? (Patrick Kusebauch @ 10.07.21)
        if(!$tokenReferenceBuilder instanceof ClassReferenceBuilder) {
            return;
        }

        if (!$node instanceof Property) {
            return;
        }

        if (!$node->type) {
            return;
        }

        $types = $this->typeResolver->resolvePropertyType($node->type);
        foreach ($types as $type) {
            $tokenReferenceBuilder->variable($type, $node->getStartLine());
        }
    }
}
