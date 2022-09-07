<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\ClassLike;

use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;

class ClassLikeReference implements TokenReferenceInterface
{
    public readonly ClassLikeType $type;

    private ?FileReference $fileReference = null;

    /**
     * @param AstInherit[]      $inherits
     * @param DependencyToken[] $dependencies
     */
    public function __construct(
        private readonly ClassLikeToken $classLikeName,
        ClassLikeType $classLikeType = null,
        public readonly array $inherits = [],
        public readonly array $dependencies = [],
        public readonly bool $isInternal = false
    ) {
        $this->type = $classLikeType ?? ClassLikeType::TYPE_CLASSLIKE;
    }

    public function withFileReference(FileReference $astFileReference): self
    {
        $instance = clone $this;
        $instance->fileReference = $astFileReference;

        return $instance;
    }

    public function getFilepath(): ?string
    {
        return $this->fileReference?->filepath;
    }

    public function getToken(): ClassLikeToken
    {
        return $this->classLikeName;
    }
}
