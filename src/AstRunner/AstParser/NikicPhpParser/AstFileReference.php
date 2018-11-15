<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInterface;

class AstFileReference implements AstFileReferenceInterface
{
    private $filepath;
    private $astClassReferences;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
        $this->astClassReferences = [];
    }

    public function addClassReference(string $className): void
    {
        $this->astClassReferences[] = new AstClassReference($className, $this);
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
}
