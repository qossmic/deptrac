<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Ast\AstMap\Function;

use Qossmic\Deptrac\Contract\Ast\TokenInterface;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
/**
 * @psalm-immutable
 */
class FunctionReference implements TokenReferenceInterface
{
    /**
     * @param DependencyToken[] $dependencies
     */
    public function __construct(private readonly \Qossmic\Deptrac\Core\Ast\AstMap\Function\FunctionToken $functionName, public readonly array $dependencies = [], private readonly ?FileReference $fileReference = null)
    {
    }
    public function withFileReference(FileReference $astFileReference) : self
    {
        return new self($this->functionName, $this->dependencies, $astFileReference);
    }
    public function getFilepath() : ?string
    {
        return $this->fileReference?->filepath;
    }
    public function getToken() : TokenInterface
    {
        return $this->functionName;
    }
}
