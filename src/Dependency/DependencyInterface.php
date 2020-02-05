<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Dependency;

use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;

interface DependencyInterface
{
    public function getClassLikeNameA(): ClassLikeName;

    public function getClassLikeNameB(): ClassLikeName;

    public function getFileOccurrence(): FileOccurrence;
}
