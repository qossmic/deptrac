<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Dependency;

use Qossmic\Deptrac\Core\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenInterface;

/**
 * @psalm-immutable
 */
interface DependencyInterface
{
    public function getDepender(): TokenInterface;

    public function getDependent(): TokenInterface;

    public function getFileOccurrence(): FileOccurrence;
}
