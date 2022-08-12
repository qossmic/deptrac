<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike;

use Qossmic\Deptrac\Contract\Ast\TokenInterface;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;

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

    public function getFilepath(): ?string
    {
        return $this->fileReference?->filepath;
    }

    public function getToken(): TokenInterface
    {
        return $this->functionName;
    }
}
