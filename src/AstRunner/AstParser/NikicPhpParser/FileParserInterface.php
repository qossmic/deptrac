<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node\Stmt;

interface FileParserInterface
{
    /**
     * @return Stmt[]
     */
    public function parse(\SplFileInfo $data): array;
}
