<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Ast;

interface TokenReferenceInterface
{
    public function getFilepath(): ?string;

    public function getToken(): TokenInterface;
}
