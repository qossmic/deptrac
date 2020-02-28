<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SplFileInfo;

interface AstParser
{
    public function parse(SplFileInfo $data): AstFileReference;
}
