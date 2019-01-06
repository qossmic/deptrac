<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

use SensioLabs\Deptrac\AstRunner\AstMap\AstInheritInterface;

interface AstParserInterface extends AstReferenceInterface
{
    public function parse($data): AstFileReferenceInterface;

    public function supports($data): bool;

    /**
     * @return AstInheritInterface[]
     */
    public function findInheritanceByClassname(string $className): array;
}
