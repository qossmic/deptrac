<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Dependency;

use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenInterface;

/**
 * @psalm-immutable
 */
class InheritDependency implements DependencyInterface
{
    private ClassLikeToken $depender;
    private TokenInterface $dependent;
    private AstInherit $path;
    private DependencyInterface $originalDependency;

    public function __construct(ClassLikeToken $depender, TokenInterface $dependent, DependencyInterface $originalDependency, AstInherit $path)
    {
        $this->depender = $depender;
        $this->dependent = $dependent;
        $this->originalDependency = $originalDependency;
        $this->path = $path;
    }

    public function getDepender(): ClassLikeToken
    {
        return $this->depender;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->getOriginalDependency()->getFileOccurrence();
    }

    public function getDependent(): TokenInterface
    {
        return $this->dependent;
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
