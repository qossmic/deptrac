<?php

namespace SensioLabs\Deptrac\DependencyResult;

use SensioLabs\AstRunner\AstMap\AstInheritInterface;

class InheritDependency implements DependencyInterface
{
    /** @var Dependency */
    private $classA;

    /** @var Dependency */
    private $classB;

    /** @var AstInheritInterface */
    private $path;

    /** @var Dependency */
    private $originalDependency;

    /**
     * @param Dependency          $classA
     * @param Dependency          $classB
     * @param DependencyInterface $originalDependency
     * @param AstInheritInterface $path
     */
    public function __construct($classA, $classB, DependencyInterface $originalDependency, AstInheritInterface $path)
    {
        $this->classA = $classA;
        $this->classB = $classB;
        $this->originalDependency = $originalDependency;
        $this->path = $path;
    }

    /**
     * @return Dependency
     */
    public function getClassA()
    {
        return $this->classA;
    }

    /**
     * @return string
     */
    public function getClassALine()
    {
        return '';
    }

    /**
     * @return Dependency
     */
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
