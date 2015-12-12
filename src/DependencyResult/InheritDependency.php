<?php

namespace DependencyTracker\DependencyResult;

use SensioLabs\AstRunner\AstMap\AstInheritInterface;

class InheritDependency implements DependencyInterface
{
    private $classA;

    private $classALine;

    /** @var Dependency */
    private $dependency;

    /** @var AstInheritInterface */
    private $path;

    /**
     * InheritDependency constructor.
     * @param $classA
     * @param $classALine
     * @param Dependency $dependency
     * @param AstInheritInterface $path
     */
    public function __construct($classA, $classALine, AstInheritInterface $path)
    {
        $this->classA = $classA;
        $this->classALine = $classALine;
        $this->path = $path;
    }


    public function getClassA()
    {
        return $this->classA;
    }

    public function getClassALine()
    {
        return $this->classALine;
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
