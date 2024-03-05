<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

/**
 * @implements ReferenceExtractorInterface<Class_>
 */
class ClassExtractor implements ReferenceExtractorInterface
{
    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if (null !== $node->name) {
            assert($referenceBuilder instanceof ClassLikeReferenceBuilder);
            if ($node->extends instanceof Name) {
                $referenceBuilder->extends($node->extends->toCodeString(), $node->extends->getLine());
            }

            foreach ($node->implements as $implement) {
                $referenceBuilder->implements($implement->toCodeString(), $implement->getLine());
            }
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        if (null !== $node->name) {
            assert($referenceBuilder instanceof ClassLikeReferenceBuilder);
            if ($node->extends instanceof Name) {
                $referenceBuilder->extends($node->extends->toCodeString(), $node->extends->getLine());
            }

            foreach ($node->implements as $implement) {
                $referenceBuilder->implements($implement->toCodeString(), $implement->getLine());
            }
        }
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }
}
