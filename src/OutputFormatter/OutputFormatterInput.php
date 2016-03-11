<?php 

namespace SensioLabs\Deptrac\OutputFormatter;

class OutputFormatterInput
{
    private $arguments;

    /**
     * @param $arguments
     */
    public function __construct($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function getArgument($name, $default = null)
    {
        if (!isset($this->arguments[$name])) {
            return $default;
        }

        return $this->arguments[$name];
    }


}
