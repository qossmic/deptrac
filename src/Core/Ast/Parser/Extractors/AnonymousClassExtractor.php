<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use DEPTRAC_202401\PhpParser\Node;
use DEPTRAC_202401\PhpParser\Node\Name;
use DEPTRAC_202401\PhpParser\Node\Stmt\Class_;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\TypeScope;
class AnonymousClassExtractor implements \Qossmic\Deptrac\Core\Ast\Parser\Extractors\ReferenceExtractorInterface
{
    public function processNode(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope) : void
    {
        if (!$node instanceof Class_ || null !== $node->name) {
            return;
        }
        if ($node->extends instanceof Name) {
            $referenceBuilder->anonymousClassExtends($node->extends->toCodeString(), $node->extends->getLine());
        }
        foreach ($node->implements as $implement) {
            $referenceBuilder->anonymousClassImplements($implement->toCodeString(), $implement->getLine());
        }
        foreach ($node->getTraitUses() as $traitUse) {
            foreach ($traitUse->traits as $trait) {
                $referenceBuilder->anonymousClassTrait($trait->toCodeString(), $trait->getLine());
            }
        }
    }
}
