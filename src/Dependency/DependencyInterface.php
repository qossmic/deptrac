<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;

interface DependencyInterface
{
    public function getClassA(): string;

    public function getClassB(): string;

    public function getFileOccurrence(): FileOccurrence;
}
