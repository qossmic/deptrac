<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Ast\AstMap\TokenInterface;

/**
 * @psalm-immutable
 */
class Dependency implements DependencyInterface
{
    private TokenInterface $depender;
    private TokenInterface $dependent;
    private FileOccurrence $fileOccurrence;

    public function __construct(TokenInterface $depender, TokenInterface $dependent, FileOccurrence $fileOccurrence)
    {
        $this->depender = $depender;
        $this->dependent = $dependent;
        $this->fileOccurrence = $fileOccurrence;
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
