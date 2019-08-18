<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstFileReference
{
    private $filepath;
    private $astClassReferences;
    private $dependencies;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
        $this->astClassReferences = [];
        $this->dependencies = [];
    }

    public function addClassReference(string $className): AstClassReference
    {
        $astClassReference = new AstClassReference($className, $this);

        $this->astClassReferences[] = $astClassReference;

        return $astClassReference;
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
        return $this->astClassReferences;
    }

    /**
     * @return AstDependency[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function addDependency(AstDependency $dependency): void
    {
        $this->dependencies[] = $dependency;
    }
}
