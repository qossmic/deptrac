<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap\ClassLike;

use Qossmic\Deptrac\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;

/**
 * @psalm-immutable
 */
class ClassLikeReference implements TokenReferenceInterface
{
    private ClassLikeToken $classLikeName;

    private ?FileReference $fileReference = null;

    /** @var DependencyToken[] */
    private array $dependencies;

    /** @var AstInherit[] */
    private array $inherits;

    /**
     * @param AstInherit[]      $inherits
     * @param DependencyToken[] $dependencies
     */
    public function __construct(ClassLikeToken $classLikeName, array $inherits = [], array $dependencies = [])
    {
        $this->classLikeName = $classLikeName;
        $this->dependencies = $dependencies;
        $this->inherits = $inherits;
    }

    public function withFileReference(FileReference $astFileReference): self
    {
        $instance = clone $this;
        $instance->fileReference = $astFileReference;

        return $instance;
    }

    public function getFileReference(): ?FileReference
    {
        return $this->fileReference;
    }

    public function getToken(): ClassLikeToken
    {
        return $this->classLikeName;
    }

    /**
     * @return DependencyToken[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @return AstInherit[]
     */
    public function getInherits(): array
    {
        return $this->inherits;
    }
}
