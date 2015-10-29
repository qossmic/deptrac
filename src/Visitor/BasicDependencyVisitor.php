<?php

namespace DependencyTracker\Visitor;

use DependencyTracker\AstMap;
use DependencyTracker\DependencyResult\Dependency;
use DependencyTracker\DependencyResult;
use PhpParser\NodeVisitor;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;

class BasicDependencyVisitor implements NodeVisitor
{
    protected $eventDispatcher;

    protected $currentKlass = '';

    protected $currentNamespace = '';

    protected $collectedUseStmts = [];

    protected $dependencyResult;

    public function __construct(DependencyResult $dependencyResult)
    {
        $this->dependencyResult = $dependencyResult;
    }

    public function analyze(AstMap $astMap)
    {
        $traverser = new \PhpParser\NodeTraverser;
        $traverser->addVisitor($this);
        $traverser->traverse($astMap->getAsts());
    }

    private function dispatchFoundDependency($className, $line)
    {
        if (!$this->currentKlass) {
            return;
        }

        $this->dependencyResult->addDependency(new Dependency(
                $this->currentNamespace.'\\'.$this->currentKlass,
                $line,
                $className
        ));
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->currentNamespace = $node->name;
        }

        if ($node instanceof Class_) {
            $this->currentKlass = $node->name;

            if ($node->extends) {
                $this->dispatchFoundDependency(
                    $node->extends->toString(),
                    $node->getLine()
                );
            }

            foreach ($node->implements as $impl) {
                $this->dispatchFoundDependency(
                    $impl->toString(),
                    $node->getLine()
                );
            }
        }

        if ($node instanceof Node\Expr\Instanceof_ && $node->class instanceof Node\Name) {
            $this->dispatchFoundDependency(
                $node->class->toString(),
                $node->getLine()
            );
        }

        if ($node instanceof Node\Stmt\UseUse) {
            $this->collectedUseStmts[] = $node;
        }
    }

    public function afterTraverse(array $nodes)
    {

    }

    public function leaveNode(Node $node)
    {

        if (!$node instanceof Class_) {
            return;
        }

        foreach ($this->collectedUseStmts as $use) {
            $this->dispatchFoundDependency($use->name->toString(), $use->getLine());
        }

        $this->collectedUseStmts = [];
        $this->currentKlass = '';
        $this->currentNamespace = '';

    }

    public function beforeTraverse(array $nodes)
    {

    }

}
