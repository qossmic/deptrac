<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Dependency;

use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\Ast\TokenInterface;
use Qossmic\Deptrac\Contract\Dependency\DependencyInterface;
class Dependency implements DependencyInterface
{
    public function __construct(private readonly TokenInterface $depender, private readonly TokenInterface $dependent, private readonly FileOccurrence $fileOccurrence, private readonly DependencyType $dependencyType)
    {
    }
    public function serialize() : array
    {
        return [['name' => $this->dependent->toString(), 'line' => $this->fileOccurrence->line]];
    }
    public function getDepender() : TokenInterface
    {
        return $this->depender;
    }
    public function getDependent() : TokenInterface
    {
        return $this->dependent;
    }
    public function getFileOccurrence() : FileOccurrence
    {
        return $this->fileOccurrence;
    }
    public function getType() : DependencyType
    {
        return $this->dependencyType;
    }
}
