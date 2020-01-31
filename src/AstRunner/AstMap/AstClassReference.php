<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstClassReference
{
    private $className;
    private $fileReference;

    /** @var AstDependency[] */
    private $dependencies;

    /** @var AstInherit[] */
    private $inherits;

    /**
     * @param AstInherit[]    $inherits
     * @param AstDependency[] $dependencies
     */
    public function __construct(ClassLikeName $className, AstFileReference $fileReference = null, array $inherits = [], array $dependencies = [])
    {
        $this->className = $className;
        $this->fileReference = $fileReference;
        $this->dependencies = $dependencies;
        $this->inherits = $inherits;
    }

    public function getFileReference(): ?AstFileReference
    {
        return $this->fileReference;
    }

    public function getClassName(): ClassLikeName
    {
        return $this->className;
    }

    /**
     * @return AstDependency[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @return AstInherit[]
     */
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
