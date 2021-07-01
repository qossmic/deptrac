<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap\AstInherit;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\AstRunner\AstMap\TokenName;

/**
 * @psalm-immutable
 */
class InheritDependency implements DependencyInterface
{
    private ClassLikeName $dependant;
    private ClassLikeName $dependee;
    private AstInherit $path;
    private DependencyInterface $originalDependency;

    public function __construct(ClassLikeName $dependant, ClassLikeName $dependee, DependencyInterface $originalDependency, AstInherit $path)
    {
        $this->dependant = $dependant;
        $this->dependee = $dependee;
        $this->originalDependency = $originalDependency;
        $this->path = $path;
    }

    public function getDependant(): TokenName
    {
        return $this->dependant;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->getOriginalDependency()->getFileOccurrence();
    }

    public function getDependee(): TokenName
    {
        return $this->dependee;
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
