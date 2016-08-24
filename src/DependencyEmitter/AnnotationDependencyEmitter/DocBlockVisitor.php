<?php

namespace SensioLabs\Deptrac\DependencyEmitter\AnnotationDependencyEmitter;

use PhpParser\Comment\Doc;
use PhpParser\NodeVisitorAbstract;
use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\AstRunner\AstMap;

class DocBlockVisitor extends NodeVisitorAbstract
{
    protected $docBlocks;

    public function __construct()
    {
        $this->docBlocks = [];
    }

    public function enterNode(\PhpParser\Node $node)
    {
        $docBlock = $node->getDocComment();
        if ($docBlock instanceof Doc) {
            $this->docBlocks[] = $docBlock;
        }
    }

    public function getDocBlocks()
    {
        return $this->docBlocks;
    }
}
