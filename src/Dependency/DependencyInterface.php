<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\AstRunner\AstMap\TokenLikeName;

interface DependencyInterface
{
    public function getTokenLikeNameA(): TokenLikeName;

    public function getTokenLikeNameB(): TokenLikeName;

    public function getFileOccurrence(): FileOccurrence;
}
