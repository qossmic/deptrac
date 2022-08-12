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
    public readonly array $classLikeReferences;

    /** @var FunctionLikeReference[] */
    public readonly array $functionLikeReferences;

    /**
     * @param ClassLikeReference[]    $classLikeReferences
     * @param FunctionLikeReference[] $functionLikeReferences
     * @param DependencyToken[]       $dependencies
     */
    public function __construct(
        public readonly string $filepath,
        array $classLikeReferences,
        array $functionLikeReferences,
        public readonly array $dependencies
    ) {
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

    public function getFileReference(): ?FileReference
    {
        return $this;
    }

    public function getToken(): TokenInterface
    {
        return new FileToken($this->filepath);
    }
}
