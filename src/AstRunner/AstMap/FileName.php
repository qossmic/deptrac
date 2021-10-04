<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

use Qossmic\Deptrac\File\FileHelper;

final class FileName implements TokenName
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = FileHelper::normalizePath($path);
    }

    public function toString(): string
    {
        $wd = getcwd();

        if (false !== $wd && 0 === strpos($this->path, $wd)) {
            $wd = FileHelper::normalizePath($wd);
            $path = substr($this->path, strlen($wd));
        } else {
            $path = $this->path;
        }

        return $path;
    }

    public function getFilepath(): string
    {
        return $this->path;
    }
}
