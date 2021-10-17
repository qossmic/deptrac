<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use LogicException;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\File\FileHelper;

class DirectoryCollector extends RegexCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'directory';
    }

    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry
    ): bool {
        $fileReference = $astTokenReference->getFileReference();

        if (null === $fileReference) {
            return false;
        }

        $filePath = $fileReference->getFilepath();
        $validatedPattern = $this->getValidatedPattern($configuration);
        $normalizedPath = FileHelper::normalizePath($filePath);

        return 1 === preg_match($validatedPattern, $normalizedPath);
    }

    protected function getPattern(array $configuration): string
    {
        if (!isset($configuration['regex']) || !is_string($configuration['regex'])) {
            throw new LogicException('DirectoryCollector needs the regex configuration.');
        }

        return '#'.$configuration['regex'].'#i';
    }
}
