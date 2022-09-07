<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\InputCollector;

use LogicException;
use Qossmic\Deptrac\Supportive\File\Exception\InvalidPathException;
use SplFileInfo;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
final class FileInputCollector implements InputCollectorInterface
{
    /**
     * @var string[]
     */
    private array $paths;

    /**
     * @param string[] $paths
     * @param string[] $excludedFilePatterns
     */
    public function __construct(array $paths, private readonly array $excludedFilePatterns, string $basePath)
    {
        $basePathInfo = new SplFileInfo($basePath);
        if (!$basePathInfo->isDir() || !$basePathInfo->isReadable()) {
            throw InvalidPathException::unreadablePath($basePathInfo);
        }
        $this->paths = [];
        foreach ($paths as $originalPath) {
            $path = Path::isRelative($originalPath)
                ? Path::makeAbsolute($originalPath, $basePathInfo->getPathname())
                : $originalPath;
            $path = new SplFileInfo($path);
            if (!$path->isReadable()) {
                throw InvalidPathException::unreadablePath($path);
            }
            $this->paths[] = Path::canonicalize($path->getPathname());
        }
    }

    /**
     * @return string[]
     */
    public function collect(): array
    {
        if ([] === $this->paths) {
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
            static fn (SplFileInfo $fileInfo) => (string) $fileInfo->getRealPath(),
            iterator_to_array($finder)
        );
    }
}
