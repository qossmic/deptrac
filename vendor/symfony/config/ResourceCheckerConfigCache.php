<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Config;

use DEPTRAC_202401\Symfony\Component\Config\Resource\ResourceInterface;
use DEPTRAC_202401\Symfony\Component\Filesystem\Exception\IOException;
use DEPTRAC_202401\Symfony\Component\Filesystem\Filesystem;
/**
 * ResourceCheckerConfigCache uses instances of ResourceCheckerInterface
 * to check whether cached data is still fresh.
 *
 * @author Matthias Pigulla <mp@webfactory.de>
 */
class ResourceCheckerConfigCache implements ConfigCacheInterface
{
    private string $file;
    /**
     * @var iterable<mixed, ResourceCheckerInterface>
     */
    private iterable $resourceCheckers;
    /**
     * @param string                                    $file             The absolute cache path
     * @param iterable<mixed, ResourceCheckerInterface> $resourceCheckers The ResourceCheckers to use for the freshness check
     */
    public function __construct(string $file, iterable $resourceCheckers = [])
    {
        $this->file = $file;
        $this->resourceCheckers = $resourceCheckers;
    }
    public function getPath() : string
    {
        return $this->file;
    }
    /**
     * Checks if the cache is still fresh.
     *
     * This implementation will make a decision solely based on the ResourceCheckers
     * passed in the constructor.
     *
     * The first ResourceChecker that supports a given resource is considered authoritative.
     * Resources with no matching ResourceChecker will silently be ignored and considered fresh.
     */
    public function isFresh() : bool
    {
        if (!\is_file($this->file)) {
            return \false;
        }
        if ($this->resourceCheckers instanceof \Traversable && !$this->resourceCheckers instanceof \Countable) {
            $this->resourceCheckers = \iterator_to_array($this->resourceCheckers);
        }
        if (!\count($this->resourceCheckers)) {
            return \true;
            // shortcut - if we don't have any checkers we don't need to bother with the meta file at all
        }
        $metadata = $this->getMetaFile();
        if (!\is_file($metadata)) {
            return \false;
        }
        $meta = $this->safelyUnserialize($metadata);
        if (\false === $meta) {
            return \false;
        }
        $time = \filemtime($this->file);
        foreach ($meta as $resource) {
            foreach ($this->resourceCheckers as $checker) {
                if (!$checker->supports($resource)) {
                    continue;
                    // next checker
                }
                if ($checker->isFresh($resource, $time)) {
                    break;
                    // no need to further check this resource
                }
                return \false;
                // cache is stale
            }
            // no suitable checker found, ignore this resource
        }
        return \true;
    }
    /**
     * Writes cache.
     *
     * @param string              $content  The content to write in the cache
     * @param ResourceInterface[] $metadata An array of metadata
     *
     * @return void
     *
     * @throws \RuntimeException When cache file can't be written
     */
    public function write(string $content, array $metadata = null)
    {
        $mode = 0666;
        $umask = \umask();
        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->file, $content);
        try {
            $filesystem->chmod($this->file, $mode, $umask);
        } catch (IOException) {
            // discard chmod failure (some filesystem may not support it)
        }
        if (null !== $metadata) {
            $filesystem->dumpFile($this->getMetaFile(), \serialize($metadata));
            try {
                $filesystem->chmod($this->getMetaFile(), $mode, $umask);
            } catch (IOException) {
                // discard chmod failure (some filesystem may not support it)
            }
        }
        if (\function_exists('opcache_invalidate') && \filter_var(\ini_get('opcache.enable'), \FILTER_VALIDATE_BOOL)) {
            @\opcache_invalidate($this->file, \true);
        }
    }
    /**
     * Gets the meta file path.
     */
    private function getMetaFile() : string
    {
        return $this->file . '.meta';
    }
    private function safelyUnserialize(string $file) : mixed
    {
        $meta = \false;
        $content = \file_get_contents($file);
        $signalingException = new \UnexpectedValueException();
        $prevUnserializeHandler = \ini_set('unserialize_callback_func', self::class . '::handleUnserializeCallback');
        $prevErrorHandler = \set_error_handler(function ($type, $msg, $file, $line, $context = []) use(&$prevErrorHandler, $signalingException) {
            if (__FILE__ === $file) {
                throw $signalingException;
            }
            return $prevErrorHandler ? $prevErrorHandler($type, $msg, $file, $line, $context) : \false;
        });
        try {
            $meta = \unserialize($content);
        } catch (\Throwable $e) {
            if ($e !== $signalingException) {
                throw $e;
            }
        } finally {
            \restore_error_handler();
            \ini_set('unserialize_callback_func', $prevUnserializeHandler);
        }
        return $meta;
    }
    /**
     * @internal
     */
    public static function handleUnserializeCallback(string $class) : void
    {
        \trigger_error('Class not found: ' . $class);
    }
}
