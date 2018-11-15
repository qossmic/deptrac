<?php

namespace SensioLabs\Deptrac\DependencyResult;

use SensioLabs\Deptrac\AstRunner\AstMap\AstInheritInterface;

class InheritDependency implements DependencyInterface
{
    private $classA;
    private $classB;
    private $path;
    private $originalDependency;

    public function __construct(string $classA, string $classB, DependencyInterface $originalDependency, AstInheritInterface $path)
    {
        $this->classA = $classA;
        $this->classB = $classB;
        $this->originalDependency = $originalDependency;
        $this->path = $path;
    }

    public function getClassA(): string
    {
        return $this->classA;
    }

    public function getClassALine(): int
    {
        return 0;
    }

    public function getClassB(): string
    {
        return $this->classB;
    }

    public function getPath(): AstInheritInterface
    {
        return $this->path;
    }

    public function getOriginalDependency(): DependencyInterface
    {
        return $this->originalDependency;
    }
}
