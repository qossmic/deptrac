<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;

interface AstFileReferenceCache
{
    public function has(string $filepath): bool;

    public function get(string $filepath): ?AstFileReference;

    public function set(AstFileReference $fileReference): void;

    public function load(): void;

    public function write(): void;
}
