<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\File;

use Qossmic\Deptrac\Supportive\File\Exception\FileAlreadyExistsException;
use Qossmic\Deptrac\Supportive\File\Exception\FileNotWritableException;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

use function is_writable;

class Dumper
{
    private readonly SplFileInfo $templateFile;

    public function __construct(string $templateFile)
    {
        $this->templateFile = new SplFileInfo($templateFile);
    }

    /**
     * @throws FileAlreadyExistsException
     * @throws FileNotWritableException
     */
    public function dump(string $file): void
    {
        $filesystem = new Filesystem();
        $target = new SplFileInfo($file);

        if ($filesystem->exists($target->getPathname())) {
            throw FileAlreadyExistsException::alreadyExists($target);
        }
        if (!is_writable($target->getPath())) {
            throw FileNotWritableException::notWritable($target);
        }

        $filesystem->copy($this->templateFile->getPathname(), $target->getPathname());
    }
}
