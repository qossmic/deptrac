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

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->currentNamespace = $node->name;
        }

        if ($node instanceof Class_) {
            $this->currentKlass = $node->name;

            if ($node->extends) {
                $this->eventDispatcher->dispatch(FoundDependencyEvent::class, new FoundDependencyEvent(
                    $node->name,
                    $node->getLine(),
                    $node->extends->toString()
                ));
            }

            foreach ($node->implements as $impl) {
                $this->eventDispatcher->dispatch(FoundDependencyEvent::class, new FoundDependencyEvent(
                    $node->name,
                    $node->getLine(),
                    $impl->toString()
                ));
            }
        }

        if ($node instanceof Node\Expr\Instanceof_) {
            $this->eventDispatcher->dispatch(FoundDependencyEvent::class, new FoundDependencyEvent(
                $node->class->toString(),
                $node->getLine(),
                $node->class->toString()
            ));
        }

        if ($node instanceof Node\Stmt\UseUse) {
            $this->eventDispatcher->dispatch(FoundDependencyEvent::class, new FoundDependencyEvent(
                $node->name,
                $node->getLine(),
                $node->name->toString()
            ));
        }
    }

    public function afterTraverse(array $nodes)
    {

    }

    public function leaveNode(Node $node)
    {

    }

    public function beforeTraverse(array $nodes)
    {

    }

}
