<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

/**
 * @implements ReferenceExtractorInterface<New_>
 */
class NewExtractor implements ReferenceExtractorInterface
{
    public function __construct(private readonly TypeResolver $typeResolver) {}

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if ($node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, $node->class) as $classLikeName) {
                $referenceBuilder->newStatement($classLikeName, $node->class->getLine());
            }
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        if ($node->class instanceof Name) {
            $referenceBuilder->newStatement($scope->resolveName($node->class), $node->class->getLine());
        }
    }

    public function getNodeType(): string
    {
        return New_::class;
    }
}
