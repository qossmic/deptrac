<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;

class AstFileReferenceInMemoryCache implements AstFileReferenceCache
{
    private $cache = [];

    public function has(string $filepath): bool
    {
        $filepath = realpath($filepath);

        return isset($this->cache[$filepath]);
    }

    public function get(string $filepath): ?AstFileReference
    {
        $filepath = realpath($filepath);

        return $this->cache[$filepath] ?? null;
    }

    public function set(AstFileReference $fileReference): void
    {
        $filepath = realpath($fileReference->getFilepath());

        $this->cache[$filepath] = $fileReference;
    }

    public function load(): void
    {
    }

    public function write(): void
    {
    }
}
