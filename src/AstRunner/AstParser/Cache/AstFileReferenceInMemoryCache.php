<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstParser\Cache;

use Qossmic\Deptrac\AstRunner\AstMap\AstFileReference;

final class AstFileReferenceInMemoryCache implements AstFileReferenceCacheInterface
{
    /**
     * @var array<string, AstFileReference>
     */
    private array $cache = [];

    public function get(string $filepath): ?AstFileReference
    {
        $filepath = realpath($filepath);

        return $this->cache[$filepath] ?? null;
    }

    public function set(AstFileReference $fileReference): void
    {
        $filepath = (string) realpath($fileReference->getFilepath());

        $this->cache[$filepath] = $fileReference;
    }
}
