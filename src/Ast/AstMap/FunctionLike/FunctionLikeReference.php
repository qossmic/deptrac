<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap\FunctionLike;

use Qossmic\Deptrac\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Ast\AstMap\TokenInterface;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;

/**
 * @psalm-immutable
 */
class FunctionLikeReference implements TokenReferenceInterface
{
    private ?FileReference $fileReference = null;
    /** @var DependencyToken[] */
    private array $dependencies;
    private FunctionLikeToken $functionName;

    /**
     * @param DependencyToken[] $dependencies
     */
    public function __construct(FunctionLikeToken $functionName, array $dependencies = [])
    {
        $this->functionName = $functionName;
        $this->dependencies = $dependencies;
    }

    /**
     * @return DependencyToken[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
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
