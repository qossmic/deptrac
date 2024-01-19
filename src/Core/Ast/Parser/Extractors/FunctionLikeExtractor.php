<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use DEPTRAC_202401\PhpParser\Node;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\TypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\TypeScope;
class FunctionLikeExtractor implements \Qossmic\Deptrac\Core\Ast\Parser\Extractors\ReferenceExtractorInterface
{
    public function __construct(private readonly TypeResolver $typeResolver)
    {
    }
    public function processNode(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope) : void
    {
        if (!$node instanceof Node\FunctionLike) {
            return;
        }
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
}
