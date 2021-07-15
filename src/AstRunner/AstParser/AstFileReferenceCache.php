<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstParser;

use Qossmic\Deptrac\AstRunner\AstMap\File\AstFileReference;

interface AstFileReferenceCache
{
    public function has(string $filepath): bool;

    public function get(string $filepath): ?AstFileReference;

    public function set(AstFileReference $fileReference): void;

    public function load(): void;

    public function write(): void;
}
