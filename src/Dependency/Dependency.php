<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;

class Dependency implements DependencyInterface
{
    private ClassLikeName $classLikeNameB;
    private ClassLikeName $classLikeNameA;
    private FileOccurrence $fileOccurrence;

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
