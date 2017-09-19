<?php

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\DependencyResult\DependencyInterface;

class DependencyResult
{
    /**
     * @var array<DependencyInterface[]>
     */
    private $dependencies = [];

    /**
     * @var array<DependencyInterface[]>
     */
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
     * @param string $className
     *
     * @return DependencyInterface[]
     */
    public function getDependenciesByClass($className)
    {
        if (!isset($this->dependencies[$className])) {
            return [];
        }

        return $this->dependencies[$className];
    }

    /**
     * @return DependencyInterface[]
     */
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
