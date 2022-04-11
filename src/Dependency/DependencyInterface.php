<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Ast\AstMap\TokenInterface;

/**
 * @psalm-immutable
 */
interface DependencyInterface
{
    public function getDepender(): TokenInterface;

    public function getDependent(): TokenInterface;

    public function getFileOccurrence(): FileOccurrence;
}
