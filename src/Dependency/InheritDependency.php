<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;

class InheritDependency implements DependencyInterface
{
    private $classLikeNameA;
    private $classLikeNameB;
    private $path;
    private $originalDependency;

    public function __construct(ClassLikeName $classLikeNameA, ClassLikeName $classLikeNameB, DependencyInterface $originalDependency, AstInherit $path)
    {
        $this->classLikeNameA = $classLikeNameA;
        $this->classLikeNameB = $classLikeNameB;
        $this->originalDependency = $originalDependency;
        $this->path = $path;
    }

    public function getClassLikeNameA(): ClassLikeName
    {
        return $this->classLikeNameA;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->getOriginalDependency()->getFileOccurrence();
    }

    public function getClassLikeNameB(): ClassLikeName
    {
        return $this->classLikeNameB;
    }

    public function getInheritPath(): AstInherit
    {
        return $this->path;
    }

    public function getOriginalDependency(): DependencyInterface
    {
        return $this->originalDependency;
    }
}
