<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;

interface AstParser
{
    public function parseFile(string $file): AstFileReference;
}
