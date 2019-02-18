<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;

interface AstParserInterface
{
    public function parse($data): AstFileReference;

    public function supports($data): bool;
}
