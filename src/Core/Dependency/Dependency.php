<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Dependency;

use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\Ast\TokenInterface;
use Qossmic\Deptrac\Contract\Dependency\DependencyInterface;

/**
 * @psalm-immutable
 */
class Dependency implements DependencyInterface
{
    public function __construct(
        private readonly TokenInterface $depender,
        private readonly TokenInterface $dependent,
        private readonly FileOccurrence $fileOccurrence
    ) {
    }

    public function getDepender(): TokenInterface
    {
        return $this->depender;
    }

    public function getDependent(): TokenInterface
    {
        return $this->dependent;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->fileOccurrence;
    }
}
