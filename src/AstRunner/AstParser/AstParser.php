<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstParser;

use Qossmic\Deptrac\AstRunner\AstMap\File\AstFileReference;
use Qossmic\Deptrac\Configuration\ConfigurationAnalyzer;

interface AstParser
{
    public function parseFile(string $file, ConfigurationAnalyzer $configuration): AstFileReference;
}
