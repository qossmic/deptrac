<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;

interface AstParser
{
    /**
     * @param mixed $data
     */
    public function parse($data): AstFileReference;

    /**
     * @param mixed $data
     */
    public function supports($data): bool;
}
