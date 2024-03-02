<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

class FunctionCallResolver implements ReferenceExtractorInterface
{
    public function __construct(private readonly TypeResolver $typeResolver) {}

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if ($node instanceof Node\Expr\FuncCall) {
            foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, $node->name) as $functionName) {
                $referenceBuilder->unresolvedFunctionCall($functionName, $node->getLine());
            }
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        if ($node instanceof Node\Expr\FuncCall) {
            $referenceBuilder->unresolvedFunctionCall($scope->resolveName($node->name), $node->getLine());
        }
    }
}
