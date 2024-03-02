<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitor;
use PHPStan\Analyser\MutatingScope;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;

/**
 * Decorates all nodes with a $scope variable.
 */
final class PHPStanScopeVisitor implements NodeVisitor
{
    private Scope $scope;

    public function __construct(
        protected NodeScopeResolver $resolver,
        protected ScopeFactory $scopeFactory,
        protected string $file
    ) {}

    public function beforeTraverse(array $nodes) {
        $this->scope = $this->scopeFactory->create(ScopeContext::create($this->file));
    }

    /**
     * @param Stmt[] $stmts
     * @param callable(Node $node, MutatingScope $scope): void $nodeCallback
     */
    private function nodeScopeResolverProcessNodes(
        array $stmts,
        MutatingScope $mutatingScope,
        callable $nodeCallback
    ): void {
        try {
            $this->resolver->processNodes($stmts, $mutatingScope, $nodeCallback);
        } catch (\Throwable $throwable) {
            if ($throwable->getMessage() !== 'Internal error.') {
                throw $throwable;
            }
        }
    }


    public function enterNode(Node $node)
    {
        // Pass to phpstan
        $this->nodeScopeResolverProcessNodes(
            [$node],
            $this->scope,
            function (Node $node, Scope $scope): void {
                // Record scope
                $this->scope = $scope;
                $node->setAttribute('scope', $scope);
            }
        );

        return null;
    }

    public function leaveNode(Node $node) {}

    public function afterTraverse(array $nodes) {}
}
