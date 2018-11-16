<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

use SensioLabs\Deptrac\AstRunner\AstMap\AstInheritInterface;

interface AstParserInterface extends AstReferenceInterface
{
    public function parse($data): AstFileReferenceInterface;

    /**
     * @return AstInheritInterface[]
     */
    public function findInheritanceByClassname(string $className): array;
}
