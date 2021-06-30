<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

class AstFileReference
{
    private string $filepath;

    /** @var AstClassReference[] */
    private array $classReferences;

    /** @var AstDependency[] */
    private array $dependencies;

    /**
     * @param AstDependency[]     $dependencies
     * @param AstClassReference[] $classReferences
     */
    public function __construct(string $filepath, array $dependencies, array $classReferences)
    {
        $this->filepath = $filepath;
        $this->dependencies = $dependencies;
        $this->classReferences = array_map(function (AstClassReference $classReference) {
            return $classReference->withFileReference($this);
        }, $classReferences);
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
}
