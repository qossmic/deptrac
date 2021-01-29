<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;

interface DependencyInterface
{
    public function getClassLikeNameA(): ClassLikeName;

    public function getClassLikeNameB(): ClassLikeName;

    public function getFileOccurrence(): FileOccurrence;
}
