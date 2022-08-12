<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\ClassLike;

use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;

class ClassLikeReference implements TokenReferenceInterface
{
    private readonly ClassLikeType $classLikeType;

    private ?FileReference $fileReference = null;

    /**
     * @param AstInherit[]      $inherits
     * @param DependencyToken[] $dependencies
     */
    public function __construct(private readonly ClassLikeToken $classLikeName, ClassLikeType $classLikeType = null, private readonly array $inherits = [], private readonly array $dependencies = [], private readonly bool $isInternal = false)
    {
        $this->classLikeType = $classLikeType ?? ClassLikeType::classLike();
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
