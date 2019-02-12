<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

interface AstParserInterface extends AstReferenceInterface
{
    public function parse($data): AstFileReferenceInterface;

    public function supports($data): bool;
}
