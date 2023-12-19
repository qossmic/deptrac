<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\Function;

use Qossmic\Deptrac\Contract\Ast\TaggedTokenReferenceInterface;
use Qossmic\Deptrac\Contract\Ast\TokenInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\TaggedReferenceTrait;

/**
 * @psalm-immutable
 */
class FunctionReference implements TaggedTokenReferenceInterface
{
    use TaggedReferenceTrait;

    /**
     * @param DependencyToken[] $dependencies
     * @param array<string,list<string>> $tags
     */
    public function __construct(
        private readonly FunctionToken $functionName,
        public readonly array $dependencies = [],
        public readonly array $tags = [],
        private readonly ?FileReference $fileReference = null
    ) {}

    public function withFileReference(FileReference $astFileReference): self
    {
        return new self(
            $this->functionName,
            $this->dependencies,
            $this->tags,
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
}
