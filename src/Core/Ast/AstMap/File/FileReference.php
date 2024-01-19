<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Ast\AstMap\File;

use Qossmic\Deptrac\Contract\Ast\TokenInterface;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Function\FunctionReference;
/**
 * @psalm-immutable
 */
class FileReference implements TokenReferenceInterface
{
    /** @var ClassLikeReference[] */
    public readonly array $classLikeReferences;
    /** @var FunctionReference[] */
    public readonly array $functionReferences;
    /**
     * @param ClassLikeReference[] $classLikeReferences
     * @param FunctionReference[] $functionReferences
     * @param DependencyToken[] $dependencies
     */
    public function __construct(public readonly string $filepath, array $classLikeReferences, array $functionReferences, public readonly array $dependencies)
    {
        /** @psalm-suppress ImpureFunctionCall */
        $this->classLikeReferences = \array_map(fn(ClassLikeReference $classReference): ClassLikeReference => $classReference->withFileReference($this), $classLikeReferences);
        /** @psalm-suppress ImpureFunctionCall */
        $this->functionReferences = \array_map(fn(FunctionReference $functionReference): FunctionReference => $functionReference->withFileReference($this), $functionReferences);
    }
    public function getFilepath() : ?string
    {
        return $this->filepath;
    }
    public function getToken() : TokenInterface
    {
        return new \Qossmic\Deptrac\Core\Ast\AstMap\File\FileToken($this->filepath);
    }
}
