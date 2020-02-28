<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

class AstClassReference
{
    private $classLikeName;
    private $fileReference;

    /** @var AstDependency[] */
    private $dependencies;

    /** @var AstInherit[] */
    private $inherits;

    /**
     * @param AstInherit[]    $inherits
     * @param AstDependency[] $dependencies
     */
    public function __construct(ClassLikeName $classLikeName, AstFileReference $fileReference = null, array $inherits = [], array $dependencies = [])
    {
        $this->classLikeName = $classLikeName;
        $this->fileReference = $fileReference;
        $this->dependencies = $dependencies;
        $this->inherits = $inherits;
    }

    public function getFileReference(): ?AstFileReference
    {
        return $this->fileReference ? clone $this->fileReference : null;
    }

    public function getClassLikeName(): ClassLikeName
    {
        return $this->classLikeName;
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
}
