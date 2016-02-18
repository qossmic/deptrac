<?php

namespace DependencyTracker\DependencyResult;

class Dependency implements DependencyInterface
{
    protected $classA;

    protected $classALine;

    protected $classB;

    /**
     * Dependency constructor.
     *
     * @param $classA
     * @param $classALine
     * @param $classB
     */
    public function __construct($classA, $classALine, $classB)
    {
        $this->classA = $classA;
        $this->classALine = $classALine;
        $this->classB = $classB;
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
}
