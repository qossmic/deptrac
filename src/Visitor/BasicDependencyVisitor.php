<?php

namespace DependencyTracker\Visitor;

use DependencyTracker\AstMap;
use DependencyTracker\Event\Visitor\FoundDependencyEvent;
use PhpParser\NodeVisitor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use DependencyTracker\CollectionMap;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;

class BasicDependencyVisitor implements NodeVisitor
{
    protected $eventDispatcher;

    protected $currentKlass = '';

    protected $currentNamespace = '';

    protected $collectedUseStmts = [];

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
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

        $this->eventDispatcher->dispatch(FoundDependencyEvent::class, new FoundDependencyEvent(
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

        if ($node instanceof Node\Expr\Instanceof_) {
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
        foreach ($this->collectedUseStmts as $use) {
            $this->dispatchFoundDependency($use->name->toString(), $use->getLine());
        }

        $this->collectedUseStmts = [];
        $this->currentKlass = '';
        $this->currentNamespace = '';
    }

    public function leaveNode(Node $node)
    {

    }

    public function beforeTraverse(array $nodes)
    {

    }

}
