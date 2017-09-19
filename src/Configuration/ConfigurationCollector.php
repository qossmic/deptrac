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
     * @param string $type
     * @param array  $args
     */
    private function __construct($type, array $args)
    {
        $this->type = $type;
        $this->args = $args;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }
}
