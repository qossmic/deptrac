<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstMap;

/**
 * @psalm-immutable
 */
final class FileName implements TokenName
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function toString(): string
    {
        return 0 === strpos($this->path, getcwd()) ? substr($this->path, strlen(getcwd())) : $this->path;
    }

    public function getFilepath(): string
    {
        return $this->path;
    }
}
