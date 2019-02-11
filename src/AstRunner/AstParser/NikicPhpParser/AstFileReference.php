<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInterface;
use SensioLabs\Deptrac\DependencyEmitter\EmittedDependency;

class AstFileReference implements AstFileReferenceInterface
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
        $this->emittedDependencies[] = new EmittedDependency($class, $line, 'use');
    }
}
