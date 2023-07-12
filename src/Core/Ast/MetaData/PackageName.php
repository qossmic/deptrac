<?php

namespace Qossmic\Deptrac\Core\Ast\MetaData;

use Qossmic\Deptrac\Contract\Ast\TokenReferenceMetaDatumInterface;

class PackageName implements TokenReferenceMetaDatumInterface
{
    public function __construct(private readonly string $packageName) {}

    public function getPackageName(): string
    {
        return $this->packageName;
    }
}
