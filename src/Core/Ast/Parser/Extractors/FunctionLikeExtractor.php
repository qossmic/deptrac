<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicTypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;
use Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser\PhpStanTypeResolver;

/**
 * @implements ReferenceExtractorInterface<Node\FunctionLike>
 */
class FunctionLikeExtractor implements ReferenceExtractorInterface
{
    public function __construct(
        private readonly NikicTypeResolver $typeResolver,
        private readonly PhpStanTypeResolver $phpStanTypeResolver,
    ) {}

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        foreach ($node->getAttrGroups() as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, $attribute->name) as $classLikeName) {
                    $referenceBuilder->attribute($classLikeName, $attribute->getLine());
                }
            }
        }
        foreach ($node->getParams() as $param) {
            if (null !== $param->type) {
                foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, $param->type) as $classLikeName) {
                    $referenceBuilder->parameter($classLikeName, $param->type->getLine());
                }
            }
        }
        $returnType = $node->getReturnType();
        if (null !== $returnType) {
            foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, $returnType) as $classLikeName) {
                $referenceBuilder->returnType($classLikeName, $returnType->getLine());
            }
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        foreach ($node->getAttrGroups() as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                $referenceBuilder->attribute($scope->resolveName($attribute->name), $attribute->getLine());
            }
        }
        foreach ($node->getParams() as $param) {
            if (null !== $param->type) {
                foreach ($this->phpStanTypeResolver->resolveType($param->type, $scope) as $item) {
                    $referenceBuilder->parameter($item, $param->type->getLine());
                }
            }
        }

        $returnType = $node->getReturnType();
        if ($returnType instanceof Node\Name) {
            $referenceBuilder->returnType($scope->resolveName($returnType), $returnType->getLine());
        }
    }

    public function getNodeType(): string
    {
        return Node\FunctionLike::class;
    }
}
