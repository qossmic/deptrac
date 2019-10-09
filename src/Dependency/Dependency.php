<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

class Dependency implements DependencyInterface
{
    protected $classA;
    protected $classALine;
    protected $classB;
    private $filename;

    public function __construct(string $filename, string $classA, int $classALine, string $classB)
    {
        $this->classA = $classA;
        $this->classALine = $classALine;
        $this->classB = $classB;
        $this->filename = $filename;
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

    public function getFilename(): string
    {
        return $this->filename;
    }
}
