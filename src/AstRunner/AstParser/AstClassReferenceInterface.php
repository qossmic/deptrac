<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

use SensioLabs\Deptrac\DependencyEmitter\EmittedDependency;

interface AstClassReferenceInterface extends AstReferenceInterface
{
    public function getFileReference(): ?AstFileReferenceInterface;

    public function getClassName(): string;

    /** @return EmittedDependency[] */
    public function getEmittedDependencies(): array;
}
