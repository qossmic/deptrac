<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

/**
 * @template T of Node
 */
interface ReferenceExtractorInterface
{
    /**
     * @return class-string<T>
     */
    public function getNodeType(): string;

    /**
     * @param T $node
     */
    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void;

    /**
     * @param T $node
     */
    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void;
}
