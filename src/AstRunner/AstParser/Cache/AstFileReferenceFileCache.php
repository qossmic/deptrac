<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstParser\Cache;

use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\AstRunner\AstMap\AstDependency;
use Qossmic\Deptrac\AstRunner\AstMap\AstFileReference;
use Qossmic\Deptrac\AstRunner\AstMap\AstFunctionReference;
use Qossmic\Deptrac\AstRunner\AstMap\AstInherit;
use Qossmic\Deptrac\AstRunner\AstMap\AstVariableReference;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\AstRunner\AstMap\FunctionName;
use Qossmic\Deptrac\AstRunner\AstMap\SuperGlobalName;
use Qossmic\Deptrac\Console\Application;
use Qossmic\Deptrac\Exception\AstRunner\AstParser\FileNotExistsException;
use Qossmic\Deptrac\File\FileReader;
use function array_filter;
use function array_map;
use function assert;
use function dirname;
use function file_exists;
use function is_readable;
use function is_writable;
use function json_decode;
use function json_encode;
use function realpath;
use function sha1_file;
use function unserialize;

class AstFileReferenceFileCache implements AstFileReferenceDeferredCacheInterface
{
    /** @var array<string, array{hash: string, reference: AstFileReference}> */
    private array $cache;
    private string $cacheFile;
    private bool $loaded = false;
    /** @var array<string, bool> */
    private array $parsedFiles = [];

    public function __construct(string $cacheFile)
    {
        $this->cache = [];
        $this->cacheFile = $cacheFile;
    }

    public function get(string $filepath): ?AstFileReference
    {
        $this->load();

        $filepath = $this->normalizeFilepath($filepath);

        if ($this->has($filepath)) {
            $this->parsedFiles[$filepath] = true;

            return $this->cache[$filepath]['reference'];
        }

        return null;
    }

    public function set(AstFileReference $fileReference): void
    {
        $this->load();

        $filepath = $this->normalizeFilepath($fileReference->getFilepath());

        $this->parsedFiles[$filepath] = true;

        $this->cache[$filepath] = [
            'hash' => (string) sha1_file($filepath),
            'reference' => $fileReference,
        ];
    }

    public function load(): void
    {
        if (true === $this->loaded) {
            return;
        }

        if (!file_exists($this->cacheFile) || !is_readable($this->cacheFile)) {
            return;
        }

        $contents = FileReader::read($this->cacheFile);

        /** @var ?array{version: string, payload: array<string, array{hash: string, reference: string}>} $cache */
        $cache = json_decode($contents, true);

        $this->loaded = true;

        if (null === $cache || Application::VERSION !== $cache['version']) {
            return;
        }

        $this->cache = array_map(
            /** @param array{hash: string, reference: string} $data */
            static function (array $data): array {
                $reference = unserialize(
                    $data['reference'],
                    [
                        'allowed_classes' => [
                            AstFileReference::class,
                            AstClassReference::class,
                            AstFunctionReference::class,
                            AstVariableReference::class,
                            AstInherit::class,
                            AstDependency::class,
                            FileName::class,
                            ClassLikeName::class,
                            FunctionName::class,
                            SuperGlobalName::class,
                            FileOccurrence::class,
                        ],
                    ]
                );
                assert($reference instanceof AstFileReference);

                return [
                    'hash' => $data['hash'],
                    'reference' => $reference,
                ];
            },
            $cache['payload']
        );
    }

    public function write(): void
    {
        if (!is_writable(dirname($this->cacheFile))) {
            return;
        }

        $cache = array_filter(
            $this->cache,
            function (string $key): bool {
                return isset($this->parsedFiles[$key]);
            },
            ARRAY_FILTER_USE_KEY
        );

        $payload = array_map(
            static function (array $data): array {
                $data['reference'] = serialize($data['reference']);

                return $data;
            },
            $cache
        );

        file_put_contents(
            $this->cacheFile,
            json_encode(
                [
                    'version' => Application::VERSION,
                    'payload' => $payload,
                ]
            )
        );
    }

    private function has(string $filepath): bool
    {
        $this->load();

        $filepath = $this->normalizeFilepath($filepath);

        if (!isset($this->cache[$filepath])) {
            return false;
        }

        $hash = sha1_file($filepath);

        if ($hash !== $this->cache[$filepath]['hash']) {
            unset($this->cache[$filepath]);

            return false;
        }

        return true;
    }

    private function normalizeFilepath(string $filepath): string
    {
        $normalized = realpath($filepath);

        if (false === $normalized) {
            throw new FileNotExistsException($filepath);
        }

        return $normalized;
    }
}
