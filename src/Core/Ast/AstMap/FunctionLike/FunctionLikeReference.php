<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike;

use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;

class FunctionLikeReference implements TokenReferenceInterface
{
    private ?FileReference $fileReference = null;

    /**
     * @param DependencyToken[] $dependencies
     */
    public function __construct(
        private readonly FunctionLikeToken $functionName,
        public readonly array $dependencies = []
    ) {
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

    public function getToken(): TokenInterface
    {
        return $this->functionName;
    }
}
