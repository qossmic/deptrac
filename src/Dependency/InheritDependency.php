<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap\AstInherit;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\AstRunner\AstMap\TokenName;

class InheritDependency implements DependencyInterface
{
    private ClassLikeName $classLikeNameA;
    private ClassLikeName $classLikeNameB;
    private AstInherit $path;
    private DependencyInterface $originalDependency;

    public function __construct(ClassLikeName $classLikeNameA, ClassLikeName $classLikeNameB, DependencyInterface $originalDependency, AstInherit $path)
    {
        $this->classLikeNameA = $classLikeNameA;
        $this->classLikeNameB = $classLikeNameB;
        $this->originalDependency = $originalDependency;
        $this->path = $path;
    }

    public function getTokenNameA(): TokenName
    {
        return $this->classLikeNameA;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->getOriginalDependency()->getFileOccurrence();
    }

    public function getTokenNameB(): TokenName
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
