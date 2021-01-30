<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstParser;

use Qossmic\Deptrac\AstRunner\AstMap\AstFileReference;

interface AstParser
{
    public function parseFile(string $file): AstFileReference;
}
