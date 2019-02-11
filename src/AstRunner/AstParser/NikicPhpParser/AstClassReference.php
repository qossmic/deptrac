<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use SensioLabs\Deptrac\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInterface;
use SensioLabs\Deptrac\DependencyEmitter\EmittedDependency;

class AstClassReference implements AstClassReferenceInterface
{
    private $className;
    private $fileReference;
    private $emittedDependecies;

    public function __construct(string $className, AstFileReference $fileReference = null)
    {
        $this->className = $className;
        $this->fileReference = $fileReference;
        $this->emittedDependecies = [];
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
        return $this->emittedDependecies;
    }

    public function addReturnType(string $class, int $line): void
    {
        $this->emittedDependecies[] = new EmittedDependency($class, $line, 'returntype');
    }

    public function addParameter(string $class, int $line): void
    {
        $this->emittedDependecies[] = new EmittedDependency($class, $line, 'parameter');
    }

    public function addNewStmt(string $class, int $line): void
    {
        $this->emittedDependecies[] = new EmittedDependency($class, $line, 'new');
    }

    public function addStaticPropertyAccess(string $class, int $line): void
    {
        $this->emittedDependecies[] = new EmittedDependency($class, $line, 'static_property');
    }

    public function addStaticMethodCall(string $class, int $line): void
    {
        $this->emittedDependecies[] = new EmittedDependency($class, $line, 'static_method');
    }

    public function addInstanceof(string $class, int $line): void
    {
        $this->emittedDependecies[] = new EmittedDependency($class, $line, 'instanceof');
    }
}
