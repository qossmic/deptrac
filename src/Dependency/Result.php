<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

class Result
{
    /** @var array<string, Dependency[]> */
    private $dependencies = [];

    /** @var array<string, InheritDependency[]> */
    private $inheritDependencies = [];

    public function addDependency(Dependency $dependency): self
    {
        if (!isset($this->dependencies[$dependency->getClassA()])) {
            $this->dependencies[$dependency->getClassA()] = [];
        }

        $this->dependencies[$dependency->getClassA()][] = $dependency;

        return $this;
    }

    public function addInheritDependency(InheritDependency $dependency): self
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
     * @return DependencyInterface[]
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
