<?php

namespace SensioLabs\Deptrac\Configuration;

class ConfigurationCollector
{
    private $type;

    private $args;

    public static function fromArray(array $arr)
    {
        if (!isset($arr['type'])) {
            throw new \InvalidArgumentException('Collector needs a type.');
        }

        return new static($arr['type'], $arr);
    }

    /**
     * ConfigurationCollector constructor.
     *
     * @param mixed $type
     * @param mixed $args
     */
    private function __construct($type, $args)
    {
        $this->type = $type;
        $this->args = $args;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }
}
