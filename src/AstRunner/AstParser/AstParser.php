<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstParser;

use Qossmic\Deptrac\AstRunner\AstMap\AstFileReference;
use Qossmic\Deptrac\Configuration\Configuration;
use Qossmic\Deptrac\Configuration\ConfigurationAnalyzer;

interface AstParser
{
    public function parseFile(string $file, ConfigurationAnalyzer $configuration): AstFileReference;
}
