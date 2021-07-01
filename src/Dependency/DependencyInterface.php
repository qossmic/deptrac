<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\AstRunner\AstMap\TokenName;

/**
 * @immutable
 */
interface DependencyInterface
{
    public function getDependant(): TokenName;

    public function getDependee(): TokenName;

    public function getFileOccurrence(): FileOccurrence;
}
