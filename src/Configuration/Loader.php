<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Configuration;

use Qossmic\Deptrac\Configuration\Loader\YmlFileLoader;
use Qossmic\Deptrac\Exception\Configuration\FileCannotBeParsedAsYamlException;
use Qossmic\Deptrac\Exception\Configuration\ParsedYamlIsNotAnArrayException;
use Qossmic\Deptrac\Exception\File\CouldNotReadFileException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Path;
use function array_filter;
use function array_key_exists;
use function array_merge;
use function dirname;
use function sprintf;
use function trigger_deprecation;

class Loader
{
    private YmlFileLoader $fileLoader;
    private Processor $processor;
    private string $workingDirectory;

    public function __construct(YmlFileLoader $fileLoader, string $workingDirectory)
    {
        $this->fileLoader = $fileLoader;
        $this->workingDirectory = $workingDirectory;
        $this->processor = new Processor();
    }

    /**
     * @throws CouldNotReadFileException
     * @throws FileCannotBeParsedAsYamlException
     * @throws ParsedYamlIsNotAnArrayException
     */
    public function load(string $file): Configuration
    {
        $absolutePath = Path::makeAbsolute($file, $this->workingDirectory);
        $depfileDirectory = dirname($absolutePath);

        $configs = [];
        $configs[] = $mainConfig = $this->normalize($this->fileLoader->parseFile($absolutePath), $absolutePath);

        $useRelativePathFromDepfile = (bool) ($mainConfig['use_relative_path_from_depfile'] ?? true);
        $basePath = $useRelativePathFromDepfile ? $depfileDirectory : $this->workingDirectory;

        if (isset($mainConfig['baseline'])) {
            $baselineFile = Path::makeAbsolute((string) $mainConfig['baseline'], $basePath);
            $configs[] = $this->normalize($this->fileLoader->parseFile($baselineFile), $baselineFile);
        }

        if (isset($mainConfig['imports'])) {
            /** @var string $importFile */
            foreach ((array) $mainConfig['imports'] as $importFile) {
                $file = Path::makeAbsolute($importFile, $basePath);
                $configs[] = $this->normalize($this->fileLoader->parseFile($file), $file);
            }
        }

        $mergedConfig = $this->processor->processConfiguration(new Definition(), $configs);

        $mergedConfig['parameters']['currentWorkingDirectory'] = $this->workingDirectory;
        $mergedConfig['parameters']['depfileDirectory'] = $depfileDirectory;

        /** @var array $analyzer */
        $analyzer = $mergedConfig['analyzer'] ?? [];
        /** @var array $analyser */
        $analyser = $mergedConfig['analyser'] ?? [];

        return Configuration::fromArray([
            'parameters' => $mergedConfig['parameters'],
            'paths' => array_map(
                static function (string $path) use ($basePath) {
                    return Path::makeAbsolute($path, $basePath);
                },
                $mergedConfig['paths']
            ),
            'exclude_files' => $mergedConfig['exclude_files'],
            'layers' => $mergedConfig['layers'],
            'ruleset' => $mergedConfig['ruleset'],
            'skip_violations' => $mergedConfig['skip_violations'],
            'ignore_uncovered_internal_classes' => $mergedConfig['ignore_uncovered_internal_classes'],
            'formatters' => $mergedConfig['formatters'] ?? [],
            'analyser' => [] === $analyser ? $analyzer : $analyser,
        ]);
    }

    private function normalize(array $config, string $filename): array
    {
        $normalized = [
            'use_relative_path_from_depfile' => $this->normalizeValue($config, 'use_relative_path_from_depfile', $filename),
            'baseline' => $this->normalizeValue($config, 'baseline', $filename),
            'formatters' => $this->normalizeValue($config, 'formatters', $filename),
            'layers' => $this->normalizeValue($config, 'layers', $filename),
            'paths' => $this->normalizeValue($config, 'paths', $filename),
            'exclude_files' => $this->normalizeValue($config, 'exclude_files', $filename),
            'ruleset' => $this->normalizeValue($config, 'ruleset', $filename),
            'skip_violations' => $this->normalizeValue($config, 'skip_violations', $filename),
            'analyser' => $this->normalizeValue($config, 'analyser', $filename),
            'ignore_uncovered_internal_classes' => $this->normalizeValue($config, 'ignore_uncovered_internal_classes', $filename),
        ];

        return array_filter(array_merge($config, $normalized), static fn ($value) => null !== $value);
    }

    /**
     * @return array|mixed|string|null
     */
    private function normalizeValue(array &$data, string $key, string $file)
    {
        /** @psalm-suppress MixedArgument */
        if (array_key_exists('parameters', $data) && array_key_exists($key, $data['parameters'])) {
            /** @var array|mixed|string|null $result */
            $result = $data['parameters'][$key];

            unset($data['parameters'][$key]);

            return $result;
        }

        if (array_key_exists($key, $data)) {
            /** @psalm-suppress TooManyArguments,UnusedFunctionCall */
            trigger_deprecation('qossmic/deptrac', '0.19.0', sprintf('Section "%s" in "%s" should be placed under parameters.', $key, $file));

            return $data[$key];
        }

        return null;
    }
}
