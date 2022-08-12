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
    public function __construct(private readonly ClassLikeToken $depender, private readonly TokenInterface $dependent, private readonly DependencyInterface $originalDependency, private readonly AstInherit $path)
    {
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
