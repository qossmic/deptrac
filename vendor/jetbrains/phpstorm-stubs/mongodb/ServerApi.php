<?php

namespace DEPTRAC_202403\MongoDB\Driver;

final class ServerApi implements \MongoDB\BSON\Serializable, \Serializable
{
    public const V1 = 1;
    public final function __construct(string $version, ?bool $strict = \false, ?bool $deprecationErrors = \false)
    {
    }
    public static function __set_state(array $properties)
    {
    }
    public final function unserialize(string $data) : void
    {
    }
    public final function serialize() : string
    {
    }
    public final function bsonSerialize() : array|object
    {
    }
}
