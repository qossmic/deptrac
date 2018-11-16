<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

interface AstFileReferenceInterface
{
    public function getFilepath(): string;

    /**
     * @return AstClassReferenceInterface[]
     */
    public function getAstClassReferences(): array;
}
