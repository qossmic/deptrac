<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

/**
 * @implements ReferenceExtractorInterface<Instanceof_>
 */
class InstanceofExtractor implements ReferenceExtractorInterface
{
    public function __construct(private readonly TypeResolver $typeResolver) {}

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if ($node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, $node->class) as $classLikeName) {
                $referenceBuilder->instanceof($classLikeName, $node->class->getLine());
            }
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        if ($node->class instanceof Name) {
            $referenceBuilder->instanceof($scope->resolveName($node->class), $node->class->getLine());
        }
    }

    public function getNodeType(): string
    {
        return Instanceof_::class;
    }
}
