<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\DependencyResult;

class Dependency implements DependencyInterface
{
    protected $classA;
    protected $classALine;
    protected $classB;

    public function __construct(string $classA, int $classALine, string $classB)
    {
        $this->classA = $classA;
        $this->classALine = $classALine;
        $this->classB = $classB;
    }

    public function getClassA(): string
    {
        return $this->classA;
    }

    public function getClassALine(): int
    {
        return $this->classALine;
    }

    public function getClassB(): string
    {
        return $this->classB;
    }
}
