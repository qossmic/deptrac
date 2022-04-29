<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\AstMap\File;

use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Ast\AstMap\FunctionLike\FunctionLikeReference;
use Qossmic\Deptrac\Ast\AstMap\TokenInterface;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;

/**
 * @psalm-immutable
 */
class FileReference implements TokenReferenceInterface
{
    private string $filepath;

    /** @var ClassLikeReference[] */
    private array $classLikeReferences;

    /** @var DependencyToken[] */
    private array $dependencies;

    /** @var FunctionLikeReference[] */
    private array $functionLikeReferences;

    /**
     * @param ClassLikeReference[]    $classLikeReferences
     * @param FunctionLikeReference[] $functionLikeReferences
     * @param DependencyToken[]       $dependencies
     */
    public function __construct(string $filepath, array $classLikeReferences, array $functionLikeReferences, array $dependencies)
    {
        $this->filepath = $filepath;
        $this->dependencies = $dependencies;
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
