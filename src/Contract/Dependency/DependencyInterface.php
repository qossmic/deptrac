<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Dependency;

use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\Ast\TokenInterface;

/**
 * @psalm-immutable
 */
interface DependencyInterface
{
    public function getDepender(): TokenInterface;

    public function getDependent(): TokenInterface;

    public function getFileOccurrence(): FileOccurrence;
}
