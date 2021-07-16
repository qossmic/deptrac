<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstParser;

use Qossmic\Deptrac\AstRunner\AstMap\AstFileReference;

class AstFileReferenceInMemoryCache implements AstFileReferenceCache
{
    /**
     * @var array<string, AstFileReference>
     */
    private array $cache = [];

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
        $filepath = (string) realpath($fileReference->getFilepath());

        $this->cache[$filepath] = $fileReference;
    }

    public function load(): void
    {
    }

    public function write(): void
    {
    }
}
