<?php

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\DependencyResult\Dependency;
use SensioLabs\Deptrac\DependencyResult\DependencyInterface;

class DependencyResult
{
    private $classLayerMap = [];

    private $dependencies = [];

    private $inheritDependencies = [];

    /**
     * @param DependencyInterface $dependency
     *
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
     *
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
     * @param string $klass
     *
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
            foreach ($deps as $dependency) {
                $buffer[] = $dependency;
            }
        }
        foreach ($this->inheritDependencies as $deps) {
            foreach ($deps as $dependency) {
                $buffer[] = $dependency;
            }
        }

        return $buffer;
    }
}
