<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\File;

use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\FunctionLike\FunctionLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;

/**
 * @psalm-immutable
 */
class FileReference implements TokenReferenceInterface
{
    /** @var ClassLikeReference[] */
    private readonly array $classLikeReferences;

    /** @var FunctionLikeReference[] */
    private readonly array $functionLikeReferences;

    /**
     * @param ClassLikeReference[]    $classLikeReferences
     * @param FunctionLikeReference[] $functionLikeReferences
     * @param DependencyToken[]       $dependencies
     */
    public function __construct(private readonly string $filepath, array $classLikeReferences, array $functionLikeReferences, private readonly array $dependencies)
    {
        /** @psalm-suppress ImpureFunctionCall */
        $this->classLikeReferences = array_map(
            fn (ClassLikeReference $classReference) => $classReference->withFileReference($this),
            $classLikeReferences
        );
        /** @psalm-suppress ImpureFunctionCall */
        $this->functionLikeReferences = array_map(
            fn (FunctionLikeReference $functionReference) => $functionReference->withFileReference($this),
            $functionLikeReferences
        );
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    /**
     * @return ClassLikeReference[]
     */
    public function getClassLikeReferences(): array
    {
        return $this->classLikeReferences;
    }

    /**
     * @return DependencyToken[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @return FunctionLikeReference[]
     */
    public function getFunctionLikeReferences(): array
    {
        return $this->functionLikeReferences;
    }

    public function getFileReference(): ?FileReference
    {
        return $this;
    }

    public function getToken(): TokenInterface
    {
        return new FileToken($this->getFilepath());
    }
}
