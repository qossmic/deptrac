<?php

namespace SensioLabs\Deptrac\DependencyResult;

class Dependency implements DependencyInterface
{
    const TYPE_EXTENDS = "extends";
    const TYPE_INSTANCEOF = "instanceof";
    const TYPE_NEW = "new";
    const TYPE_PARAMETER = "parameter";
    const TYPE_RETURN = "return";
    const TYPE_STATIC_METHOD = "static_method";
    const TYPE_STATIC_PROPERTY = "static_property";
    const TYPE_USE = "use";

    protected $classA;

    protected $classALine;

    protected $classB;

    protected $type;

    /**
     * Dependency constructor.
     *
     * @param $classA
     * @param $classALine
     * @param $classB
     * @param string $type
     */
    public function __construct($classA, $classALine, $classB, $type = self::TYPE_USE)
    {
        $this->classA = $classA;
        $this->classALine = $classALine;
        $this->classB = $classB;
        $this->type = $type;
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
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }
}
