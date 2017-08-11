<?php

namespace SensioLabs\Deptrac\DependencyResult;

class Dependency implements DependencyInterface
{
    private $classA;
    private $classALine;
    private $classB;

    /**
     * Dependency constructor.
     *
     * @param string $classA
     * @param string $classALine
     * @param string $classB
     */
    public function __construct($classA, $classALine, $classB)
    {
        $this->classA = $classA;
        $this->classALine = $classALine;
        $this->classB = $classB;
    }

    /**
     * @return string
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
        return $this->classALine;
    }

    /**
     * @return string
     */
    public function getClassB()
    {
        return $this->classB;
    }
}
