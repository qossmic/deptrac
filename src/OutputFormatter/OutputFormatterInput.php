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
    public function getOption($name, $default = null)
    {
        if (!isset($this->options[$name])) {
            return $default;
        }

        return $this->options[$name];
    }


}
