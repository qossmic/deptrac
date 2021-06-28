<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

interface AstTokenReference
{
    public function getFileReference(): ?AstFileReference;

    public function getTokenName(): TokenName;
}
