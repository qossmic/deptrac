<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Config\Resource;

/**
 * FileExistenceResource represents a resource stored on the filesystem.
 * Freshness is only evaluated against resource creation or deletion.
 *
 * The resource can be a file or a directory.
 *
 * @author Charles-Henri Bruyand <charleshenri.bruyand@gmail.com>
 *
 * @final
 */
class FileExistenceResource implements SelfCheckingResourceInterface
{
    private string $resource;
    private bool $exists;
    /**
     * @param string $resource The file path to the resource
     */
    public function __construct(string $resource)
    {
        $this->resource = $resource;
        $this->exists = \file_exists($resource);
    }
    public function __toString() : string
    {
        return 'existence.' . $this->resource;
    }
    public function getResource() : string
    {
        return $this->resource;
    }
    public function isFresh(int $timestamp) : bool
    {
        return \file_exists($this->resource) === $this->exists;
    }
}
