<?php 

namespace DependencyTracker;

class Layer
{

    protected $collectors = [];

    /**
     * Layer constructor.
     * @param array $collectors
     */
    public function __construct(array $collectors)
    {
        $this->collectors = $collectors;
    }

    /**
     * @return array
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

}
