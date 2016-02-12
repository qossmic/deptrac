<?php

namespace DependencyTracker\DependencyResult;

use SensioLabs\AstRunner\AstMap\AstInheritInterface;

class InheritDependency implements DependencyInterface
{
    private $classA;

    private $classALine;

    /** @var AstInheritInterface */
    private $path;

    /**
     * @param $classA
     * @param AstInheritInterface $path
     */
    public function __construct($classA, AstInheritInterface $path)
    {
        $this->classA = $classA;
        $this->path = $path;
    }


    public function getClassA()
    {
        return $this->classA;
    }

    public function getClassALine()
    {
        return $this->path->getLine();
    }

    public function getClassB()
    {
        return $this->path->getClassName();
    }

    /**
     * @return AstInheritInterface
     */
    public function getPath()
    {
        return $this->path;
    }


}
