<?php

namespace Qossmic\Deptrac\AstRunner\AstParser\Cache;

interface AstFileReferenceDeferredCacheInterface extends AstFileReferenceCacheInterface
{
    public function load(): void;

    public function write(): void;
}
