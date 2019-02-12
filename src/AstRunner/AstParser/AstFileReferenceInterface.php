<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

use SensioLabs\Deptrac\AstRunner\AstMap\AstDependency;

interface AstFileReferenceInterface
{
    public function getFilepath(): string;

    /**
     * @return AstClassReferenceInterface[]
     */
    public function getAstClassReferences(): array;

    /** @return AstDependency[] */
    public function getEmittedDependencies(): array;
}
