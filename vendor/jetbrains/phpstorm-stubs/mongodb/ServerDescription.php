<?php

namespace DEPTRAC_202403\MongoDB\Driver;

/**
 * @since 1.13.0
 */
final class ServerDescription
{
    public const TYPE_UNKNOWN = 'Unknown';
    public const TYPE_STANDALONE = 'Standalone';
    public const TYPE_MONGOS = 'Mongos';
    public const TYPE_POSSIBLE_PRIMARY = 'PossiblePrimary';
    public const TYPE_RS_PRIMARY = 'RSPrimary';
    public const TYPE_RS_SECONDARY = 'RSSecondary';
    public const TYPE_RS_ARBITER = 'RSArbiter';
    public const TYPE_RS_OTHER = 'RSOther';
    public const TYPE_RS_GHOST = 'RSGhost';
    public const TYPE_LOAD_BALANCER = 'LoadBalancer';
    private final function __construct()
    {
    }
    /**
     * Returns the server's most recent "hello" response
     * @link https://www.php.net/manual/en/mongodb-driver-serverdescription.gethelloresponse.php
     */
    public final function getHelloResponse() : array
    {
    }
    /**
     * Returns the hostname of this server
     * @link https://www.php.net/manual/en/mongodb-driver-serverdescription.gethost.php
     */
    public final function getHost() : string
    {
    }
    /**
     * Returns the server's last update time in microseconds
     * @link https://www.php.net/manual/en/mongodb-driver-serverdescription.getlastupdatetime.php
     */
    public final function getLastUpdateTime() : int
    {
    }
    /**
     * Returns the port on which this server is listening
     * @link https://www.php.net/manual/en/mongodb-driver-serverdescription.getport.php
     */
    public final function getPort() : int
    {
    }
    /**
     * Returns the server's round trip time in milliseconds
     * @link https://www.php.net/manual/en/mongodb-driver-serverdescription.getroundtriptime.php
     */
    public final function getRoundTripTime() : ?int
    {
    }
    /**
     * Returns a string denoting the type of this server
     * @link https://www.php.net/manual/en/mongodb-driver-serverdescription.gettype.php
     */
    public final function getType() : string
    {
    }
    public final function __wakeup() : void
    {
    }
}
