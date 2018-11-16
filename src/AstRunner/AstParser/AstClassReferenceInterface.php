<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser;

interface AstClassReferenceInterface extends AstReferenceInterface
{
    public function getFileReference(): ?AstFileReferenceInterface;

    public function getClassName(): string;
}
