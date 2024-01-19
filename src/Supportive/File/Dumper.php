<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Supportive\File;

use Qossmic\Deptrac\Supportive\File\Exception\FileAlreadyExistsException;
use Qossmic\Deptrac\Supportive\File\Exception\FileNotExistsException;
use Qossmic\Deptrac\Supportive\File\Exception\FileNotWritableException;
use Qossmic\Deptrac\Supportive\File\Exception\IOException;
use SplFileInfo;
use DEPTRAC_202401\Symfony\Component\Filesystem\Exception\FileNotFoundException;
use DEPTRAC_202401\Symfony\Component\Filesystem\Exception\IOException as SymfonyIOException;
use DEPTRAC_202401\Symfony\Component\Filesystem\Filesystem;
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
     * @throws FileNotExistsException
     * @throws IOException
     */
    public function dump(string $file) : void
    {
        $filesystem = new Filesystem();
        $target = new SplFileInfo($file);
        if ($filesystem->exists($target->getPathname())) {
            throw FileAlreadyExistsException::alreadyExists($target);
        }
        if (!is_writable($target->getPath())) {
            throw FileNotWritableException::notWritable($target);
        }
        try {
            $filesystem->copy($this->templateFile->getPathname(), $target->getPathname());
        } catch (FileNotFoundException) {
            throw FileNotExistsException::fromFilePath($this->templateFile->getPathname());
        } catch (SymfonyIOException $e) {
            throw IOException::couldNotCopy($e->getMessage());
        }
    }
}
