<?php

namespace DEPTRAC_202403\MongoDB\Driver;

use MongoDB\BSON\Serializable;
use MongoDB\Driver\Exception\InvalidArgumentException;
use MongoDB\Driver\Exception\UnexpectedValueException;
/**
 * MongoDB\Driver\ReadConcern controls the level of isolation for read operations for replica sets and replica set shards. This option requires the WiredTiger storage engine and MongoDB 3.2 or later.
 * @link https://php.net/manual/en/class.mongodb-driver-readconcern.php
 * @since 1.1.0
 */
final class ReadConcern implements Serializable, \Serializable
{
    /**
     * @since 1.2.0
     */
    public const LINEARIZABLE = 'linearizable';
    public const LOCAL = 'local';
    public const MAJORITY = 'majority';
    /**
     * @since 1.4.0
     */
    public const AVAILABLE = 'available';
    /**
     * @since 1.11.0
     */
    public const SNAPSHOT = 'snapshot';
    /**
     * Construct immutable ReadConcern
     * @link https://php.net/manual/en/mongodb-driver-readconcern.construct.php
     */
    public final function __construct(?string $level = null)
    {
    }
    public static function __set_state(array $properties)
    {
    }
    /**
     * Returns the ReadConcern's "level" option
     * @link https://php.net/manual/en/mongodb-driver-readconcern.getlevel.php
     * @return string|null
     * @since 1.0.0
     */
    public final function getLevel() : ?string
    {
    }
    /**
     * Returns an object for BSON serialization
     * @link https://php.net/manual/en/mongodb-driver-readconcern.bsonserialize.php
     * @since 1.2.0
     */
    public final function bsonSerialize() : array|object
    {
    }
    /**
     * Checks if this is the default read concern
     * @link https://secure.php.net/manual/en/mongodb-driver-readconcern.isdefault.php
     * @since 1.3.0
     * @throws \MongoDB\Driver\Exception\InvalidArgumentException On argument parsing errors.
     */
    public final function isDefault() : bool
    {
    }
    /**
     * Serialize a ReadConcern
     * @since 1.7.0
     * @link https://php.net/manual/en/mongodb-driver-readconcern.serialize.php
     * @throws InvalidArgumentException
     */
    public final function serialize() : string
    {
    }
    /**
     * Unserialize a ReadConcern
     * @since 1.7.0
     * @link https://php.net/manual/en/mongodb-driver-readconcern.unserialize.php
     * @throws InvalidArgumentException on argument parsing errors or if the properties are invalid
     * @throws UnexpectedValueException if the properties cannot be unserialized (i.e. serialized was malformed)
     */
    public final function unserialize(string $data) : void
    {
    }
}
