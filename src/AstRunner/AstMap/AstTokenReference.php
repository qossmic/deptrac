<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

use Qossmic\Deptrac\AstRunner\AstMap\File\AstFileReference;

/**
 * @psalm-immutable
 */
interface AstTokenReference
{
    public function getFileReference(): ?AstFileReference;

    public function getTokenName(): TokenName;
}
