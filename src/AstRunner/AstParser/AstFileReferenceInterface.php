<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

use SensioLabs\Deptrac\DependencyEmitter\EmittedDependency;

interface AstFileReferenceInterface
{
    public function getFilepath(): string;

    /**
     * @return AstClassReferenceInterface[]
     */
    public function getAstClassReferences(): array;

    /** @return EmittedDependency[] */
    public function getEmittedDependencies(): array;
}
