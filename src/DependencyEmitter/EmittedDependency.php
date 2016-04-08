<?php

namespace SensioLabs\Deptrac\DependencyEmitter;

class EmittedDependency
{
    private $class;

    private $line;

    private $type;

    /**
     * EmittedDependency constructor.
     *
     * @param $class
     * @param $line
     * @param $type
     */
    public function __construct($class, $line, $type)
    {
        $this->class = $class;
        $this->line = $line;
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return mixed
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }
}
