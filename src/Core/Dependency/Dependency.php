<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Dependency;

use Qossmic\Deptrac\Core\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenInterface;

/**
 * @psalm-immutable
 */
class Dependency implements DependencyInterface
{
    public function __construct(private readonly TokenInterface $depender, private readonly TokenInterface $dependent, private readonly FileOccurrence $fileOccurrence)
    {
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
