<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Contract\Dependency;

use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\Ast\TokenInterface;
/**
 * Represents a dependency between 2 tokens (depender and dependent).
 */
interface DependencyInterface
{
    public function getDepender() : TokenInterface;
    public function getDependent() : TokenInterface;
    public function getFileOccurrence() : FileOccurrence;
    /**
     * @return array<array{name:string, line:int}>
     */
    public function serialize() : array;
    public function getType() : DependencyType;
}
