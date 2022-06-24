<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Utils\File;

use Qossmic\Deptrac\Utils\File\Exception\FileAlreadyExistsException;
use Qossmic\Deptrac\Utils\File\Exception\FileNotWritableException;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

class Dumper
{
    private SplFileInfo $templateFile;

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
        if (!$target->isWritable()) {
            throw FileNotWritableException::notWritable($target);
        }

        $filesystem->copy($this->templateFile->getPathname(), $target->getPathname());
    }
}
