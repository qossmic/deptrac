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

        $path = false !== $wd && 0 === strpos($this->path, $wd) ? substr($this->path, strlen($wd)) : $this->path;

        // make paths/patterns cross-OS compatible
        return str_replace('\\', '/', $path);
    }

    public function getFilepath(): string
    {
        // make paths/patterns cross-OS compatible
        return str_replace('\\', '/', $this->path);
    }
}
