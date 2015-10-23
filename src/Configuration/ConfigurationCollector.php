<?php

namespace DependencyTracker\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationCollector
{
    private $type;

    private $args;

    public static function fromArray(array $arr)
    {
        $options = (new OptionsResolver())->setRequired([
            'type',
        ])->setDefaults([
            'args' => []
        ])->resolve($arr);

        return new static($options['type'], $options['args']);
    }

    /**
     * ConfigurationCollector constructor.
     * @param $type
     * @param $args
     */
    public function __construct($type, $args)
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
