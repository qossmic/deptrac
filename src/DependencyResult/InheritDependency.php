<?php

namespace DependencyTracker\DependencyResult;

use SensioLabs\AstRunner\AstMap\AstInheritInterface;

class InheritDependency implements DependencyInterface
{
    private $classA;

    /** @var Dependency */
    private $classB;

    /** @var AstInheritInterface */
    private $path;

    /** @var Dependency */
    private $originalDependency;

    /**
     * @param $classA
     * @param AstInheritInterface $path
     */
    public function __construct($classA, $classB, DependencyInterface $originalDependency, AstInheritInterface $path)
    {
        $this->classA = $classA;
        $this->classB = $classB;
        $this->originalDependency = $originalDependency;
        $this->path = $path;
    }

    public function getClassA()
    {
        return $this->classA;
    }

    public function getClassALine()
    {
        return '';
    }

    public function getClassB()
    {
        return $this->classB;
    }

    /**
     * @return AstInheritInterface
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return Dependency
     */
    public function getOriginalDependency()
    {
        return $this->originalDependency;
    }
}
