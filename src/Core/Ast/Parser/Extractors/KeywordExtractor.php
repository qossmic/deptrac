<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Catch_;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

class KeywordExtractor implements ReferenceExtractorInterface
{
    public function __construct(private readonly TypeResolver $typeResolver) {}

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if ($node instanceof Node\Stmt\TraitUse && $referenceBuilder instanceof ClassLikeReferenceBuilder) {
            foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, ...$node->traits) as $classLikeName) {
                $referenceBuilder->trait($classLikeName, $node->getLine());
            }

            return;
        }

        if ($node instanceof Instanceof_ && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, $node->class) as $classLikeName) {
                $referenceBuilder->instanceof($classLikeName, $node->class->getLine());
            }

            return;
        }

        if ($node instanceof New_ && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, $node->class) as $classLikeName) {
                $referenceBuilder->newStatement($classLikeName, $node->class->getLine());
            }

            return;
        }

        if ($node instanceof Catch_) {
            foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, ...$node->types) as $classLikeName) {
                $referenceBuilder->catchStmt($classLikeName, $node->getLine());
            }

            return;
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        if ($node instanceof Node\Stmt\TraitUse && $referenceBuilder instanceof ClassLikeReferenceBuilder) {
            foreach ($node->traits as $trait) {
                $referenceBuilder->trait($scope->resolveName($trait), $node->getLine());
            }

            return;
        }

        if ($node instanceof Instanceof_ && $node->class instanceof Name) {
            $referenceBuilder->instanceof($scope->resolveName($node->class), $node->class->getLine());

            return;
        }

        if ($node instanceof New_ && $node->class instanceof Name) {
            $referenceBuilder->newStatement($scope->resolveName($node->class), $node->class->getLine());

            return;
        }

        if ($node instanceof Catch_) {
            foreach ($node->types as $classLikeName) {
                $referenceBuilder->catchStmt($scope->resolveName($classLikeName), $node->getLine());
            }

            return;
        }
    }
}
