<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Cache;

use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeType;
use Qossmic\Deptrac\Core\Ast\AstMap\DependencyToken;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Function\FunctionReference;
use Qossmic\Deptrac\Core\Ast\AstMap\Function\FunctionToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\SuperGlobalToken;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\VariableReference;
use Qossmic\Deptrac\Supportive\DependencyInjection\Exception\CacheFileException;
use Qossmic\Deptrac\Supportive\File\Exception\CouldNotReadFileException;
use Qossmic\Deptrac\Supportive\File\Exception\FileNotExistsException;
use Qossmic\Deptrac\Supportive\File\FileReader;

use function array_filter;
use function array_map;
use function assert;
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
    /** @var array<string, array{hash: string, reference: FileReference}> */
    private array $cache = [];
    private bool $loaded = false;
    /** @var array<string, bool> */
    private array $parsedFiles = [];

    public function __construct(
        private readonly string $cacheFile,
        private readonly string $cacheVersion
    ) {
        $this->cache = [];
    }

    public function get(string $filepath): ?FileReference
    {
        $this->load();

        /** @throws void */
        $filepath = $this->normalizeFilepath($filepath);

        /** @throws void */
        if ($this->has($filepath)) {
            $this->parsedFiles[$filepath] = true;

            return $this->cache[$filepath]['reference'];
        }

        return null;
    }

    public function set(FileReference $fileReference): void
    {
        $this->load();

        /** @throws void */
        $filepath = $this->normalizeFilepath($fileReference->filepath);

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

        try {
            $contents = FileReader::read($this->cacheFile);
        } catch (CouldNotReadFileException) {
            return;
        }

        /** @var ?array{version: string, payload: array<string, array{hash: string, reference: string}>} $cache */
        $cache = json_decode($contents, true);

        $this->loaded = true;

        if (null === $cache || $this->cacheVersion !== $cache['version']) {
            return;
        }

        $this->cache = array_map(
            /** @param array{hash: string, reference: string} $data */
            static function (array $data): array {
                $reference = unserialize(
                    $data['reference'],
                    [
                        'allowed_classes' => [
                            FileReference::class,
                            ClassLikeReference::class,
                            FunctionReference::class,
                            VariableReference::class,
                            AstInherit::class,
                            DependencyToken::class,
                            DependencyType::class,
                            FileToken::class,
                            ClassLikeToken::class,
                            ClassLikeType::class,
                            FunctionToken::class,
                            SuperGlobalToken::class,
                            FileOccurrence::class,
                        ],
                    ]
                );
                assert($reference instanceof FileReference);

                return [
                    'hash' => $data['hash'],
                    'reference' => $reference,
                ];
            },
            $cache['payload']
        );
    }

    /**
     * @throws CacheFileException
     */
    public function write(): void
    {
        if (
            !file_exists($this->cacheFile)
            && !touch($this->cacheFile)
            && !is_writable($this->cacheFile)
        ) {
            throw CacheFileException::notWritable($this->cacheFile);
        }

        $cache = array_filter(
            $this->cache,
            fn (string $key): bool => isset($this->parsedFiles[$key]),
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
                    'version' => $this->cacheVersion,
                    'payload' => $payload,
                ]
            )
        );
    }

    /**
     * @throws FileNotExistsException
     */
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

    /**
     * @throws FileNotExistsException
     */
    private function normalizeFilepath(string $filepath): string
    {
        $normalized = realpath($filepath);

        if (false === $normalized) {
            throw FileNotExistsException::fromFilePath($filepath);
        }

        return $normalized;
    }
}
