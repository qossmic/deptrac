<?php

namespace DEPTRAC_202403\MongoDB\BSON;

use DEPTRAC_202403\JetBrains\PhpStorm\Deprecated;
use MongoDB\Driver\Exception\InvalidArgumentException;
use MongoDB\Driver\Exception\UnexpectedValueException;
/**
 * BSON type for the "Symbol" type. This BSON type is deprecated, and this class can not be instantiated. It will be created from a
 * BSON symbol type while converting BSON to PHP, and can also be converted back into BSON while storing documents in the database.
 *
 * @link https://secure.php.net/manual/en/class.mongodb-bson-symbol.php
 */
#[Deprecated]
final class Symbol implements Type, \Serializable, \JsonSerializable
{
    private final function __construct()
    {
    }
    /**
     * Serialize a Symbol
     * @since 1.2.0
     * @link https://www.php.net/manual/en/mongodb-bson-symbol.serialize.php
     * @throws InvalidArgumentException
     */
    public final function serialize() : string
    {
    }
    /**
     * Unserialize a Symbol
     * @since 1.2.0
     * @link https://www.php.net/manual/en/mongodb-bson-symbol.unserialize.php
     * @throws InvalidArgumentException on argument parsing errors or if the properties are invalid
     * @throws UnexpectedValueException if the properties cannot be unserialized (i.e. serialized was malformed)
     */
    public final function unserialize(string $data) : void
    {
    }
    /**
     * Returns a representation that can be converted to JSON
     * @since 1.2.0
     * @link https://www.php.net/manual/en/mongodb-bson-symbol.jsonserialize.php
     * @return mixed data which can be serialized by json_encode()
     * @throws InvalidArgumentException on argument parsing errors
     */
    public final function jsonSerialize()
    {
    }
    /**
     * Returns the Symbol as a string
     * @return string Returns the string representation of this Symbol.
     */
    public final function __toString() : string
    {
    }
}
