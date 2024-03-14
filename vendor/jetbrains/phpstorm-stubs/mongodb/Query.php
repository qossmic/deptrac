<?php

namespace DEPTRAC_202403\MongoDB\Driver;

use MongoDB\Driver\Exception\InvalidArgumentException;
/**
 * The MongoDB\Driver\Query class is a value object that represents a database query.
 * @link https://php.net/manual/en/class.mongodb-driver-query.php
 */
final class Query
{
    /**
     * Construct new Query
     * @link https://php.net/manual/en/mongodb-driver-query.construct.php
     * @param array|object $filter The search filter.
     * @param array|null $queryOptions
     * @throws InvalidArgumentException on argument parsing errors.
     */
    public final function __construct(array|object $filter, ?array $queryOptions = null)
    {
    }
    public final function __wakeup()
    {
    }
}
