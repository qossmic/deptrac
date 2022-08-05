<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\ClassLike;

use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;

class ClassLikeReference implements TokenReferenceInterface
{
    private ClassLikeToken $classLikeName;
    private ClassLikeType $classLikeType;

    private ?FileReference $fileReference = null;

    /** @var DependencyToken[] */
    private array $dependencies;

    /** @var AstInherit[] */
    private array $inherits;

    private bool $isInternal;

    /**
     * @param AstInherit[]      $inherits
     * @param DependencyToken[] $dependencies
     */
    public function __construct(ClassLikeToken $classLikeName, ClassLikeType $classLikeType = null, array $inherits = [], array $dependencies = [], bool $isInternal = false)
    {
        $this->classLikeName = $classLikeName;
        $this->classLikeType = $classLikeType ?? ClassLikeType::classLike();
        $this->dependencies = $dependencies;
        $this->inherits = $inherits;
        $this->isInternal = $isInternal;
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

    public function getType(): ClassLikeType
    {
        return $this->classLikeType;
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

    public function isInternal(): bool
    {
        return $this->isInternal;
    }
}
