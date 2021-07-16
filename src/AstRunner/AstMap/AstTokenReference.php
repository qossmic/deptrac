<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

/**
 * @psalm-immutable
 */
interface AstTokenReference
{
    public function getFileReference(): ?AstFileReference;

    public function getTokenName(): TokenName;
}
