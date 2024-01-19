<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use DEPTRAC_202401\PhpParser\Node;
use DEPTRAC_202401\PhpParser\Node\Stmt\Property;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\TypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\TypeScope;
class PropertyExtractor implements \Qossmic\Deptrac\Core\Ast\Parser\Extractors\ReferenceExtractorInterface
{
    public function __construct(private readonly TypeResolver $typeResolver)
    {
    }
    public function processNode(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope) : void
    {
        if (!$node instanceof Property) {
            return;
        }
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, $attribute->name) as $classLikeName) {
                    $referenceBuilder->attribute($classLikeName, $attribute->getLine());
                }
            }
        }
        if (null !== $node->type) {
            foreach ($this->typeResolver->resolvePropertyType($node->type) as $type) {
                $referenceBuilder->variable($type, $node->type->getStartLine());
            }
        }
    }
}
