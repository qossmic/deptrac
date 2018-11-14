<?php

namespace SensioLabs\Deptrac\Dependency;

use SensioLabs\Deptrac\DependencyResult\Dependency;
use SensioLabs\Deptrac\DependencyResult\DependencyInterface;

class Result
{
    private $dependencies = [];

    private $inheritDependencies = [];

    public function addDependency(DependencyInterface $dependency): self
    {
        if (!isset($this->dependencies[$dependency->getClassA()])) {
            $this->dependencies[$dependency->getClassA()] = [];
        }

        $this->dependencies[$dependency->getClassA()][] = $dependency;

        return $this;
    }

    public function addInheritDependency(DependencyInterface $dependency): self
    {
        if (!isset($this->inheritDependencies[$dependency->getClassA()])) {
            $this->inheritDependencies[$dependency->getClassA()] = [];
        }

        $this->inheritDependencies[$dependency->getClassA()][] = $dependency;

        return $this;
    }

    /**
     * @return Dependency[]
     */
    public function getDependenciesByClass(string $className): array
    {
        return $this->dependencies[$className] ?? [];
    }

    /**
     * @return Dependency[]
     */
    public function getDependenciesAndInheritDependencies(): array
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
