<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\SuperGlobalToken;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

class VariableExtractor implements ReferenceExtractorInterface
{
    /**
     * @var list<string>
     */
    private array $allowedNames;

    public function __construct()
    {
        $this->allowedNames = SuperGlobalToken::allowedNames();
    }

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if ($node instanceof Node\Expr\Variable && in_array($node->name, $this->allowedNames, true)) {
            $referenceBuilder->superglobal($node->name, $node->getLine());
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        if ($node instanceof Node\Expr\Variable && in_array($node->name, $this->allowedNames, true)) {
            $referenceBuilder->superglobal($node->name, $node->getLine());
        }
    }
}
