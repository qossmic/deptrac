<?php

namespace DependencyTracker;

use DependencyTracker\DependencyResult\Dependency;
use DependencyTracker\DependencyResult\DependencyInterface;

class DependencyResult
{

    private $classLayerMap = [];

    private $dependencies = [];

    private $inheritDependencies = [];

    /**
     * @param DependencyInterface $dependency
     * @return $this
     */
    public function addDependency(DependencyInterface $dependency)
    {
        if (!isset($this->dependencies[$dependency->getClassA()])) {
            $this->dependencies[$dependency->getClassA()] = [];
        }

        $this->dependencies[$dependency->getClassA()][] = $dependency;

        return $this;
    }

    /**
     * @param DependencyInterface $dependency
     * @return $this
     */
    public function addInheritDependency(DependencyInterface $dependency)
    {
        if (!isset($this->inheritDependencies[$dependency->getClassA()])) {
            $this->inheritDependencies[$dependency->getClassA()] = [];
        }

        $this->inheritDependencies[$dependency->getClassA()][] = $dependency;

        return $this;
    }

    /**
     * @param $klass
     * @return Dependency[]
     */
    public function getDependenciesByClass($klass)
    {
        if (!isset($this->dependencies[$klass])) {
            return [];
        }

        return $this->dependencies[$klass];
    }

    /** @return Dependency[] */
    public function getDependenciesAndInheritDependencies()
    {
        $buffer = [];

        foreach ($this->dependencies as $deps) {
            foreach($deps as $dependency) {
                $buffer[] = $dependency;
            }
        }
        foreach ($this->inheritDependencies as $deps) {
            foreach($deps as $dependency) {
                $buffer[] = $dependency;
            }
        }

        return $buffer;
    }

    /**
     * @param $klass
     * @param $layer
     * @return $this
     */
    public function addClassToLayer($klass, $layer)
    {
        if (!isset($this->classLayerMap[$klass])) {
            $this->classLayerMap[$klass] = [];
        }

        $this->classLayerMap[$klass][] = $layer;

        return $this;
    }

    public function getClassLayerMap()
    {
        return $this->classLayerMap;
    }

    public function getLayersByClassName($className)
    {
        if (!isset($this->classLayerMap[$className])) {
            return [];
        }

        return $this->classLayerMap[$className];
    }

}
