<?php

namespace Qossmic\Deptrac\Core\Ast\Parser\Cache;

interface AstFileReferenceDeferredCacheInterface extends AstFileReferenceCacheInterface
{
    public function load(): void;

    public function write(): void;
}
