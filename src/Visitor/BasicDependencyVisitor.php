<?php

namespace DependencyTracker\Visitor;

use DependencyTracker\AstHelper;
use DependencyTracker\AstMap;
use DependencyTracker\DependencyResult\Dependency;
use DependencyTracker\DependencyResult;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
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

        if ($node instanceof Node\Stmt\ClassLike) {
            $this->currentKlass = $node->name;

            foreach (AstHelper::findInheritances($node) as $inherit) {
                $this->dispatchFoundDependency(
                    $inherit,
                    0
                );
            }
        }

        if ($node instanceof Node\Expr\Instanceof_ && $node->class instanceof Node\Name) {
            $this->dispatchFoundDependency(
                $node->class->toString(),
                $node->getLine()
            );
        }

        // @todo new is missing
        // @todo function argument is missing

        if ($node instanceof Node\Stmt\UseUse) {
            $this->collectedUseStmts[] = $node;
        }

        $a = 0;
    }

    public function afterTraverse(array $nodes)
    {

    }

    public function leaveNode(Node $node)
    {

        if (!$node instanceof Class_ && !$node instanceof Interface_) {
            return;
        }

        foreach ($this->collectedUseStmts as $use) {
            $this->dispatchFoundDependency($use->name->toString(), $use->getLine());
        }

        $this->collectedUseStmts = [];
        $this->currentKlass = '';

    }

    public function beforeTraverse(array $nodes)
    {

    }

}
