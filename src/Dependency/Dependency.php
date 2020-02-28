<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;

class Dependency implements DependencyInterface
{
    private $classLikeNameB;
    private $classLikeNameA;
    private $fileOccurrence;

    public function __construct(ClassLikeName $classLikeNameA, ClassLikeName $classLikeNameB, FileOccurrence $fileOccurrence)
    {
        $this->classLikeNameA = $classLikeNameA;
        $this->classLikeNameB = $classLikeNameB;
        $this->fileOccurrence = $fileOccurrence;
    }

    public function getClassLikeNameA(): ClassLikeName
    {
        return $this->classLikeNameA;
    }

    public function getClassLikeNameB(): ClassLikeName
    {
        return $this->classLikeNameB;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->fileOccurrence;
    }
}
