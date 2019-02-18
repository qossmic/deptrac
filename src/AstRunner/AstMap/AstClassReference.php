<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstClassReference
{
    private $className;
    private $fileReference;
    private $dependencies;
    private $inherits;

    public function __construct(string $className, AstFileReference $fileReference = null)
    {
        $this->className = $className;
        $this->fileReference = $fileReference;
        $this->dependencies = [];
        $this->inherits = [];
    }

    public function getFileReference(): ?AstFileReference
    {
        return $this->fileReference;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getInherits(): array
    {
        return $this->inherits;
    }

    public function addDependency(AstDependency $dependency): void
    {
        $this->dependencies[] = $dependency;
    }

    public function addInherit(AstInherit $inherit): void
    {
        $this->inherits[] = $inherit;
    }
}
