<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap\File;

use Qossmic\Deptrac\Core\Ast\AstMap\TokenInterface;
use Symfony\Component\Filesystem\Path;

final class FileToken implements TokenInterface
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = Path::normalize($path);
    }

    public function toString(): string
    {
        $wd = getcwd();

        if (false !== $wd) {
            $wd = Path::normalize($wd);
        }

        if (false !== $wd && 0 === strpos($this->path, $wd)) {
            return substr($this->path, strlen($wd));
        }

        return $this->path;
    }

    public function getFilepath(): string
    {
        return $this->path;
    }
}
