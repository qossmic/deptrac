<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\JsonDecoder;
use SensioLabs\Deptrac\Console\Application;

class CacheableFileParser implements FileParserInterface
{
    private $parser;
    private $cache;
    private $cacheFile;
    private $loaded = false;
    private $jsonDecoder;

    public function __construct(FileParser $parser)
    {
        $this->parser = $parser;
        $this->cache = [];
        $this->cacheFile = getcwd().'/.deptrac.cache';
        $this->jsonDecoder = new JsonDecoder();
    }

    public function parse(\SplFileInfo $file): array
    {
        $this->load();

        $realPath = $file->getRealPath();
        $hash = sha1_file($realPath);

        if (isset($this->cache[$realPath]) && $hash === $this->cache[$realPath]['hash']) {
            return $this->cache[$realPath]['ast'];
        }

        $ast = $this->parser->parse($file);
        $this->cache[$realPath] = ['hash' => $hash, 'ast' => $ast];

        return $ast;
    }

    public function load(): void
    {
        if ($this->loaded) {
            return;
        }

        if (!file_exists($this->cacheFile) || !is_readable($this->cacheFile)) {
            return;
        }

        $cache = json_decode(file_get_contents($this->cacheFile), true);

        $this->loaded = true;

        if (Application::VERSION !== $cache['version']) {
            return;
        }

        $this->cache = array_map(
            function (array $data) {
                $data['ast'] = $this->jsonDecoder->decode($data['ast']);

                return $data;
            },
            $cache['payload']
        );
    }

    public function write(): void
    {
        if (!is_writable(\dirname($this->cacheFile))) {
            return;
        }

        file_put_contents(
            $this->cacheFile,
            json_encode(
                [
                    'version' => Application::VERSION,
                    'payload' => array_map(
                        function (array $data) {
                            $data['ast'] = json_encode($data['ast']);

                            return $data;
                        },
                        $this->cache
                    ),
                ]
            )
        );
    }
}
