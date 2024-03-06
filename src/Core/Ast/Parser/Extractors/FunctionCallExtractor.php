<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicTypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;
use Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser\PhpStanTypeResolver;

/**
 * @implements ReferenceExtractorInterface<\PhpParser\Node\Expr\FuncCall>
 */
class FunctionCallExtractor implements ReferenceExtractorInterface
{
    public function __construct(
        private readonly NikicTypeResolver $typeResolver,
        private readonly PhpStanTypeResolver $phpStanTypeResolver,
    ) {}

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, $node->name) as $functionName) {
            $referenceBuilder->unresolvedFunctionCall($functionName, $node->getLine());
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        foreach ($this->phpStanTypeResolver->resolveType($node->name, $scope) as $functionName) {
            $referenceBuilder->unresolvedFunctionCall($functionName, $node->getLine());
        }
    }

    public function getNodeType(): string
    {
        return Node\Expr\FuncCall::class;
    }
}
