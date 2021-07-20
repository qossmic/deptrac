<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

final class FileName implements TokenName
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function toString(): string
    {
        $wd = getcwd();

        return false !== $wd && 0 === strpos($this->path, $wd) ? substr($this->path, strlen($wd)) : $this->path;
    }

    public function getFilepath(): string
    {
        return $this->path;
    }
}
