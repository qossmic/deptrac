<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Ast\AstMap\ClassLike;

use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
/**
 * @psalm-immutable
 */
class ClassLikeReference implements TokenReferenceInterface
{
    public readonly \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType $type;
    /**
     * @param AstInherit[] $inherits
     * @param DependencyToken[] $dependencies
     */
    public function __construct(private readonly \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken $classLikeName, \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType $classLikeType = null, public readonly array $inherits = [], public readonly array $dependencies = [], public readonly bool $isInternal = \false, private readonly ?FileReference $fileReference = null)
    {
        $this->type = $classLikeType ?? \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType::TYPE_CLASSLIKE;
    }
    public function withFileReference(FileReference $astFileReference) : self
    {
        return new self($this->classLikeName, $this->type, $this->inherits, $this->dependencies, $this->isInternal, $astFileReference);
    }
    public function getFilepath() : ?string
    {
        return $this->fileReference?->filepath;
    }
    public function getToken() : \Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken
    {
        return $this->classLikeName;
    }
}
