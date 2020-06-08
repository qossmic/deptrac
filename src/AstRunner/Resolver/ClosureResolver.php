<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Resolver;

use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

class ClosureResolver implements ClassDependencyResolver
{
    private TypeResolver $typeResolver;

    public function __construct(TypeResolver $typeResolver)
    {
        $this->typeResolver = $typeResolver;
    }

    public function processNode(Node $node, ClassReferenceBuilder $classReferenceBuilder, Context $context): void
    {
        if (!$node instanceof Node\Expr\Closure) {
            return;
        }

        $params = array_filter(
            array_map(
                static function (Node\Param $param) {
                    return $param->type;
                },
                $node->params
            )
        );

        foreach ($this->typeResolver->resolvePHPParserTypes($context, ...$params) as $classLikeName) {
            $classReferenceBuilder->parameter($classLikeName, $node->getLine());
        }

        if (null !== $node->returnType) {
            foreach ($this->typeResolver->resolvePHPParserTypes($context, $node->returnType) as $classLikeName) {
                $classReferenceBuilder->returnType($classLikeName, $node->returnType->getLine());
            }
        }
    }
}
