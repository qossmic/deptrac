<?php 

namespace SensioLabs\Deptrac\OutputFormatter;

class OutputFormatterInput
{
    private $options;

    /**
     * @param $arguments
     */
    public function __construct($arguments)
    {
        $this->options = $arguments;
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function getOption($name)
    {
        if (!isset($this->options[$name])) {
            throw new \InvalidArgumentException('option '.$name.' is not configured.');
        }

        return $this->options[$name];
    }


}
