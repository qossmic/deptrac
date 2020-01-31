<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;

class Dependency implements DependencyInterface
{
    private $classB;
    private $classA;
    private $fileOccurrence;

    public function __construct(ClassLikeName $classA, ClassLikeName $classB, FileOccurrence $fileOccurrence)
    {
        $this->classA = $classA;
        $this->classB = $classB;
        $this->fileOccurrence = $fileOccurrence;
    }

    public function getClassA(): ClassLikeName
    {
        return $this->classA;
    }

    public function getClassB(): ClassLikeName
    {
        return $this->classB;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->fileOccurrence;
    }
}
