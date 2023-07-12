<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\Function;

use Qossmic\Deptrac\Contract\Ast\TokenInterface;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceMetaDatumInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;

/**
 * @psalm-immutable
 */
class FunctionReference implements TokenReferenceInterface
{
    /**
     * @param TokenReferenceMetaDatumInterface[] $metaData
     * @param DependencyToken[] $dependencies
     */
    public function __construct(
        private readonly FunctionToken $functionName,
        public readonly array $metaData = [],
        public readonly array $dependencies = [],
        private readonly ?FileReference $fileReference = null
    ) {}

    public function withFileReference(FileReference $astFileReference): self
    {
        return new self(
            $this->functionName,
            $this->metaData,
            $this->dependencies,
            $astFileReference
        );
    }

    public function getFilepath(): ?string
    {
        return $this->fileReference?->filepath;
    }

    public function getToken(): TokenInterface
    {
        return $this->functionName;
    }

    public function getMetaData(): array
    {
        return $this->metaData;
    }
}
