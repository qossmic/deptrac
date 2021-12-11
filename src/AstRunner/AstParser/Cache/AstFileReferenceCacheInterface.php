<?php

namespace Qossmic\Deptrac\AstRunner\AstParser\Cache;

use Qossmic\Deptrac\AstRunner\AstMap\AstFileReference;

interface AstFileReferenceCacheInterface
{
    public function get(string $filepath): ?AstFileReference;

    public function set(AstFileReference $fileReference): void;
}
