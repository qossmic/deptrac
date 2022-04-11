<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Ast\Parser;

use Qossmic\Deptrac\Ast\AstMap\File\FileReference;

interface ParserInterface
{
    public function parseFile(string $file): FileReference;
}
