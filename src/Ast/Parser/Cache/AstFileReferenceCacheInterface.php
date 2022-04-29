<?php

namespace Qossmic\Deptrac\Ast\Parser\Cache;

use Qossmic\Deptrac\Ast\AstMap\File\FileReference;

interface AstFileReferenceCacheInterface
{
    public function get(string $filepath): ?FileReference;

    public function set(FileReference $fileReference): void;
}
