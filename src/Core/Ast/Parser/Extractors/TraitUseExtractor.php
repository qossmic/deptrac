<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicTypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

/**
 * @implements ReferenceExtractorInterface<\PhpParser\Node\Stmt\TraitUse>
 */
class TraitUseExtractor implements ReferenceExtractorInterface
{
    public function __construct(private readonly NikicTypeResolver $typeResolver) {}

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if ($referenceBuilder instanceof ClassLikeReferenceBuilder) {
            foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, ...$node->traits) as $classLikeName) {
                $referenceBuilder->trait($classLikeName, $node->getLine());
            }
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        if ($referenceBuilder instanceof ClassLikeReferenceBuilder) {
            foreach ($node->traits as $trait) {
                $referenceBuilder->trait($scope->resolveName($trait), $node->getLine());
            }
        }
    }

    public function getNodeType(): string
    {
        return Node\Stmt\TraitUse::class;
    }
}
