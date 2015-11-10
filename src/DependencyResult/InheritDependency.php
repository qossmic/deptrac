<?php

namespace DependencyTracker\DependencyResult;

class InheritDependency
{
    protected $classA;

    protected $classALine;

    protected $classB;

    protected $dependencies = [];

    /**
     * InheritDependency constructor.
     * @param $classA
     * @param $classALine
     * @param $classB
     * @param array $dependencies
     */
    public function __construct($classA, $classALine, $classB, array $dependencies)
    {
        $this->classA = $classA;
        $this->classALine = $classALine;
        $this->classB = $classB;
        $this->dependencies = $dependencies;
    }

    /**
     * @return mixed
     */
    public function getClassA()
    {
        return $this->classA;
    }

    /**
     * @return mixed
     */
    public function getClassALine()
    {
        return $this->classALine;
    }

    /**
     * @return mixed
     */
    public function getClassB()
    {
        return $this->classB;
    }

    /**
     * @return Dependency[]
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }



}
