<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap\File;

use Qossmic\Deptrac\AstRunner\AstMap\AstDependency;
use Qossmic\Deptrac\AstRunner\AstMap\AstTokenReference;
use Qossmic\Deptrac\AstRunner\AstMap\ClassToken\AstClassReference;
use Qossmic\Deptrac\AstRunner\AstMap\FunctionToken\AstFunctionReference;
use Qossmic\Deptrac\AstRunner\AstMap\TokenName;

class AstFileReference implements AstTokenReference
{
    private string $filepath;

    /** @var AstClassReference[] */
    private array $classReferences;

    /** @var AstDependency[] */
    private array $dependencies;

    /** @var AstFunctionReference[] */
    private array $functionReferences;

    /**
     * @param AstClassReference[] $classReferences
     * @param AstDependency[]     $dependencies
     */
    public function __construct(string $filepath, array $classReferences, array $functionReferences, array $dependencies)
    {
        $this->filepath = $filepath;
        $this->dependencies = $dependencies;
        $this->classReferences = array_map(
            fn (AstClassReference $classReference) => $classReference->withFileReference($this),
            $classReferences
        );
        $this->functionReferences = array_map(
            fn (AstFunctionReference $functionReference) => $functionReference->withFileReference($this),
            $functionReferences
        );
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    /**
     * @return AstClassReference[]
     */
    public function getAstClassReferences(): array
    {
        return $this->classReferences;
    }

    /**
     * @return AstDependency[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @return AstFunctionReference[]
     */
    public function getFunctionReferences(): array
    {
        return $this->functionReferences;
    }

    public function getFileReference(): ?AstFileReference
    {
        return $this;
    }

    public function getTokenName(): TokenName
    {
        return new FileName($this->getFilepath());
    }
}
