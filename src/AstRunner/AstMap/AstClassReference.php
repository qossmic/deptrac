<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

use SensioLabs\Deptrac\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInterface;

class AstClassReference implements AstClassReferenceInterface
{
    private $className;
    private $fileReference;
    private $emittedDependencies;
    private $inherits;

    public function __construct(string $className, AstFileReference $fileReference = null)
    {
        $this->className = $className;
        $this->fileReference = $fileReference;
        $this->emittedDependencies = [];
        $this->inherits = [];
    }

    public function getFileReference(): ?AstFileReferenceInterface
    {
        return $this->fileReference;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getEmittedDependencies(): array
    {
        return $this->emittedDependencies;
    }

    public function getInherits(): array
    {
        return $this->inherits;
    }

    public function addReturnType(string $class, int $line): void
    {
        $this->emittedDependencies[] = new AstDependency($class, $line, 'returntype');
    }

    public function addParameter(string $class, int $line): void
    {
        $this->emittedDependencies[] = new AstDependency($class, $line, 'parameter');
    }

    public function addNewStmt(string $class, int $line): void
    {
        $this->emittedDependencies[] = new AstDependency($class, $line, 'new');
    }

    public function addStaticPropertyAccess(string $class, int $line): void
    {
        $this->emittedDependencies[] = new AstDependency($class, $line, 'static_property');
    }

    public function addStaticMethodCall(string $class, int $line): void
    {
        $this->emittedDependencies[] = new AstDependency($class, $line, 'static_method');
    }

    public function addInstanceof(string $class, int $line): void
    {
        $this->emittedDependencies[] = new AstDependency($class, $line, 'instanceof');
    }

    public function addInherit(AstInherit $inherit): void
    {
        $this->inherits[] = $inherit;
    }
}
