<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

class AstFileReference
{
    private string $filepath;

    /** @var AstClassReference[] */
    private array $classReferences;

    /**
     * @var AstDependency[]
     *
     * @deprecated
     */
    private array $dependencies;

    /**
     * @param AstClassReference[] $classReferences
     * @param AstDependency[]     $dependencies
     */
    public function __construct(string $filepath, array $classReferences, array $dependencies)
    {
        $this->filepath = $filepath;
        $this->dependencies = $dependencies;
        $this->classReferences = array_map(
            fn (AstClassReference $classReference) => $classReference->withFileReference($this),
            $classReferences
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
     *
     * @deprecated
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }
}
