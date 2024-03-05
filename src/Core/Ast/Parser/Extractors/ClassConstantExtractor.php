<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

/**
 * @implements ReferenceExtractorInterface<ClassConstFetch>
 */
class ClassConstantExtractor implements ReferenceExtractorInterface
{
    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if (!$node->class instanceof Name || $node->class->isSpecialClassName()) {
            return;
        }

        $referenceBuilder->constFetch($node->class->toCodeString(), $node->class->getLine());
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        if (!$node->class instanceof Name || $node->class->isSpecialClassName()) {
            return;
        }

        $referenceBuilder->constFetch($node->class->toCodeString(), $node->class->getLine());
    }

    public function getNodeType(): string
    {
        return ClassConstFetch::class;
    }
}
