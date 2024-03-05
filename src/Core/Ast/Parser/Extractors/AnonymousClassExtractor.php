<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

/**
 * @implements ReferenceExtractorInterface<Class_>
 */
class AnonymousClassExtractor implements ReferenceExtractorInterface
{
    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if (null !== $node->name) {
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

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        if (null !== $node->name) {
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

    public function getNodeType(): string
    {
        return Class_::class;
    }
}
