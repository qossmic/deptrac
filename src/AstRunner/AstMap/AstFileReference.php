<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstFileReference
{
    private $filepath;
    private $astClassReferences;
    private $emittedDependencies;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
        $this->astClassReferences = [];
        $this->emittedDependencies = [];
    }

    public function addClassReference(string $className): AstClassReference
    {
        return $this->astClassReferences[] = new AstClassReference($className, $this);
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

    public function getEmittedDependencies(): array
    {
        return $this->emittedDependencies;
    }

    public function addUse(string $class, int $line): void
    {
        $this->emittedDependencies[] = new AstDependency($class, $line, 'use');
    }
}
