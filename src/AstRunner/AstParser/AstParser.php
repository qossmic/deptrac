<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstParser;

use Qossmic\Deptrac\AstRunner\AstMap\AstFileReference;
use Qossmic\Deptrac\Configuration\Configuration;

interface AstParser
{
    public function parseFile(string $file, ?Configuration $configuration): AstFileReference;
}
