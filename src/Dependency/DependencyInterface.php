<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;

interface DependencyInterface
{
    public function getClassA(): ClassLikeName;

    public function getClassB(): ClassLikeName;

    public function getFileOccurrence(): FileOccurrence;
}
