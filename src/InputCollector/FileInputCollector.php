<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\InputCollector;

use LogicException;
use Qossmic\Deptrac\File\Exception\InvalidPathException;
use SplFileInfo;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

final class FileInputCollector implements InputCollectorInterface
{
    /**
     * @var string[]
     */
    private array $paths;

    /**
     * @var string[]
     */
    private array $excludedFilePatterns;

    /**
     * @param string[] $paths
     * @param string[] $excludedFilePatterns
     */
    public function __construct(array $paths, array $excludedFilePatterns, string $basePath)
    {
        $basePath = new SplFileInfo($basePath);
        if (!$basePath->isDir() || !$basePath->isReadable()) {
            throw InvalidPathException::unreadablePath($basePath);
        }
        $this->paths = [];
        foreach ($paths as $originalPath) {
            $path = Path::isRelative($originalPath)
                ? Path::makeAbsolute($originalPath, $basePath->getPathname())
                : $originalPath;
            $path = new SplFileInfo($path);
            if (!$path->isReadable()) {
                throw InvalidPathException::unreadablePath($path);
            }
            $this->paths[] = Path::canonicalize($path->getPathname());
        }
        $this->excludedFilePatterns = $excludedFilePatterns;
    }

    /**
     * @return string[]
     */
    public function collect(): array
    {
        if ($this->paths === []) {
            throw new LogicException("No 'paths' defined in the depfile.");
        }

        $finder = (new Finder())
            ->in($this->paths)
            ->name('*.php')
            ->files()
            ->followLinks()
            ->ignoreUnreadableDirs()
            ->ignoreVCS(true)
            ->notPath($this->excludedFilePatterns);

        $customFilterIterator = $finder->getIterator();

        $finder = new PathNameFilterIterator($customFilterIterator, [], $this->excludedFilePatterns);

        return array_map(
            static function (SplFileInfo $fileInfo) {
                return (string) $fileInfo->getRealPath();
            },
            iterator_to_array($finder)
        );
    }
}
