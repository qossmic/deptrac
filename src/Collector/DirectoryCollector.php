<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use LogicException;
use Qossmic\Deptrac\AstRunner\AstMap;
use Symfony\Component\Filesystem\Path;

class DirectoryCollector extends RegexCollector implements CollectorInterface
{
    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        array $resolutionTable = []
    ): bool {
        $fileReference = $astTokenReference->getFileReference();

        if (null === $fileReference) {
            return false;
        }

        $filePath = $fileReference->getFilepath();
        $validatedPattern = $this->getValidatedPattern($configuration);
        $normalizedPath = Path::normalize($filePath);

        return 1 === preg_match($validatedPattern, $normalizedPath);
    }

    protected function getPattern(array $configuration): string
    {
        if (isset($configuration['regex']) && !isset($configuration['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'ClassNameCollector should use the "value" key from this version');
            $configuration['value'] = $configuration['regex'];
        }

        if (!isset($configuration['value']) || !is_string($configuration['value'])) {
            throw new LogicException('DirectoryCollector needs the regex configuration.');
        }

        return '#'.$configuration['value'].'#i';
    }
}
