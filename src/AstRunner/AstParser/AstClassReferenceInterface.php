<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;

interface AstClassReferenceInterface extends AstReferenceInterface
{
    public function getFileReference(): ?AstFileReferenceInterface;

    public function getClassName(): string;

    /** @return \SensioLabs\Deptrac\AstRunner\AstMap\AstDependency[] */
    public function getEmittedDependencies(): array;

    /** @return AstInherit[] */
    public function getInherits(): array;
}
