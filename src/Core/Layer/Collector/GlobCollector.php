<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use LogicException;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\TokenReferenceInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Glob;

final class GlobCollector extends RegexCollector
{
    private readonly string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = Path::normalize($basePath);
    }

    public function satisfy(array $config, TokenReferenceInterface $reference, AstMap $astMap): bool
    {
        $fileReference = $reference->getFileReference();

        if (null === $fileReference) {
            return false;
        }

        $validatedPattern = $this->getValidatedPattern($config);
        $normalizedPath = Path::normalize($fileReference->filepath);
        $relativeFilePath = Path::makeRelative($normalizedPath, $this->basePath);

        return 1 === preg_match($validatedPattern, $relativeFilePath);
    }

    protected function getPattern(array $config): string
    {
        if (!isset($config['value']) || !is_string($config['value'])) {
            throw new LogicException('GlobCollector needs the glob pattern configuration.');
        }

        return Glob::toRegex($config['value']);
    }
}
