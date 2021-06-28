<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\AstRunner\AstMap\TokenName;

interface DependencyInterface
{
    public function getTokenNameA(): TokenName;

    public function getTokenNameB(): TokenName;

    public function getFileOccurrence(): FileOccurrence;
}
