<?php

namespace Qossmic\Deptrac\Ast\Parser\Cache;

interface AstFileReferenceDeferredCacheInterface extends AstFileReferenceCacheInterface
{
    public function load(): void;

    public function write(): void;
}
