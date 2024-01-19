<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Ast\AstMap\File;

use Qossmic\Deptrac\Contract\Ast\TokenInterface;
use DEPTRAC_202401\Symfony\Component\Filesystem\Path;
final class FileToken implements TokenInterface
{
    public readonly string $path;
    public function __construct(string $path)
    {
        $this->path = Path::normalize($path);
    }
    public function toString() : string
    {
        $wd = \getcwd();
        if (\false !== $wd) {
            $wd = Path::normalize($wd);
        }
        if (\false !== $wd && \str_starts_with($this->path, $wd)) {
            return \substr($this->path, \strlen($wd));
        }
        return $this->path;
    }
}
