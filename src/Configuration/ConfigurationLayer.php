<?php

namespace SensioLabs\Deptrac\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationLayer
{
    /**
     * @var ConfigurationCollector[]
     */
    private $collectors;

    /**
     * @var string
     */
    private $name;

    public static function fromArray(array $arr)
    {
        $options = (new OptionsResolver())
            ->setRequired(
                [
                    'name',
                    'collectors',
                ]
            )
            ->resolve($arr);

        return new static(
            array_map(
                function ($v) {
                    return ConfigurationCollector::fromArray($v);
                },
                $options['collectors']
            ),
            $options['name']
        );
    }

    /**
     * @param ConfigurationCollector[] $collectors
     * @param string                   $name
     */
    private function __construct(array $collectors, $name)
    {
        $this->collectors = $collectors;
        $this->name = $name;
    }

    /**
     * @return ConfigurationCollector[]
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
