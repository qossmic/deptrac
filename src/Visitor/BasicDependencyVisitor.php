<?php

namespace DependencyTracker\Visitor;


use DependencyTracker\CollectionMap;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;

class BasicDependencyVisitor extends \PhpParser\NodeVisitorAbstract
{
    protected $collectionMap;
    protected $currentKlass;
    protected $currentNamespace;

    protected $collectedUseStmts = [];

    public function __construct(CollectionMap $collectionMap)
    {
        $this->collectionMap = $collectionMap;
    }

    public function leaveNode(Node $node)
    {

    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->currentNamespace = $node->name;
        }

        if ($node instanceof Class_) {
            $this->currentKlass = $node->name;

            if ($node->extends) {
                $this->collectedUseStmts[] = $node->extends->toString();
            }

            foreach ($node->implements as $impl) {
                $this->collectedUseStmts[] = $impl->toString();
            }
        }

        if ($node instanceof Node\Expr\Instanceof_) {
            $this->collectedUseStmts[] = $node->class->toString();
        }

        if ($node instanceof Node\Stmt\UseUse) {
            $this->collectedUseStmts[] = $node->name->toString();
        }
    }

    public function afterTraverse(array $nodes)
    {
        foreach ($this->collectedUseStmts as $use) {
            $this->collectionMap->addDependency($this->currentNamespace.'\\'.$this->currentKlass, $use);
        }

        $this->collectedUseStmts = [];
    }


}