<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;

class Dependency implements DependencyInterface
{
    private $classB;
    private $classA;
    private $fileOccurrence;

    public function __construct(string $classA, string $classB, FileOccurrence $fileOccurrence)
    {
        $this->classA = $classA;
        $this->classB = $classB;
        $this->fileOccurrence = $fileOccurrence;
    }

    public function getClassA(): string
    {
        return $this->classA;
    }

    public function getClassB(): string
    {
        return $this->classB;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->fileOccurrence;
    }
}
