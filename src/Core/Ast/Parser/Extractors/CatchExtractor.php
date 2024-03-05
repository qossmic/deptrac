<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PhpParser\Node\Stmt\Catch_;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

/**
 * @implements ReferenceExtractorInterface<Catch_>
 */
class CatchExtractor implements ReferenceExtractorInterface
{
    public function __construct(private readonly TypeResolver $typeResolver) {}

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, ...$node->types) as $classLikeName) {
            $referenceBuilder->catchStmt($classLikeName, $node->getLine());
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        foreach ($node->types as $classLikeName) {
            $referenceBuilder->catchStmt($scope->resolveName($classLikeName), $node->getLine());
        }
    }

    public function getNodeType(): string
    {
        return Catch_::class;
    }
}
