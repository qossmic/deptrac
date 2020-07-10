<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstFileReference
{
    /** @var string */
    private $filepath;

    /** @var AstClassReference[] */
    private $classReferences;

    /** @var AstDependency[] */
    private $dependencies;

    /**
     * @param AstDependency[]     $dependencies
     * @param AstClassReference[] $classReferences
     */
    public function __construct(string $filepath, array $dependencies, array $classReferences)
    {
        $this->filepath = $filepath;
        $this->dependencies = $dependencies;
        $this->classReferences = array_map(fn (AstClassReference $classReference) => $classReference->withFileReference($this), $classReferences);
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
