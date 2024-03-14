<?php

namespace DEPTRAC_202403\Relay;

/**
 * Relay Sentinel client.
 *
 * @see https://redis.io/docs/management/sentinel/
 */
class Sentinel
{
    /**
     * Whether to throw an exception on `-ERR` replies.  Default: false
     *
     * @var int
     */
    public const OPT_THROW_ON_ERR = 1;
    /**
     * Whether \Relay\Sentinel should automatically discover other sentinels in the
     * cluster, so it may use them if we fail to communicate with the first one.
     *
     * @var int
     */
    public const OPT_AUTO_DISCOVER = 2;
    /**
     * Establishes a new connection to a Sentinel instance.
     *
     * For backwards compatibility with PhpRedis 6.x, the
     * constructor may be called with a single options array.
     *
     * @param  array|string|null  $host
     * @param  int  $port
     * @param  float  $timeout
     * @param  mixed  $persistent
     * @param  int  $retry_interval
     * @param  float  $read_timeout
     * @param  mixed  $auth
     */
    #[\DEPTRAC_202403\Relay\Attributes\Server]
    public function __construct(array|string|null $host = null, int $port = 26379, float $timeout = 0, mixed $persistent = null, int $retry_interval = 0, float $read_timeout = 0, #[\SensitiveParameter] mixed $auth = null)
    {
    }
    /**
     * Check if the current Sentinel configuration is able to reach the quorum needed
     * to failover a master, and the majority needed to authorize the failover.
     *
     * @param  string  $master
     * @return bool
     */
    #[\DEPTRAC_202403\Relay\Attributes\RedisCommand]
    public function ckquorum(string $master) : bool
    {
    }
    /**
     * Force a failover as if the master was not reachable,
     * and without asking for agreement to other Sentinels.
     *
     * @param  string  $master
     * @return bool
     */
    #[\DEPTRAC_202403\Relay\Attributes\RedisCommand]
    public function failover(string $master) : bool
    {
    }
    /**
     * Force Sentinel to rewrite its configuration on disk,
     * including the current Sentinel state.
     *
     * @return bool
     */
    #[\DEPTRAC_202403\Relay\Attributes\RedisCommand]
    public function flushconfig() : bool
    {
    }
    /**
     * Returns the ip and port number of the master with that name.
     *
     * @param  string  $master
     * @return array|false
     */
    #[\DEPTRAC_202403\Relay\Attributes\RedisCommand]
    public function getMasterAddrByName(string $master) : array|false
    {
    }
    /**
     * Returns the state and info of the specified master.
     *
     * @param  string  $master
     * @return array|false
     */
    #[\DEPTRAC_202403\Relay\Attributes\RedisCommand]
    public function master(string $master) : array|false
    {
    }
    /**
     * Returns a list of monitored masters and their state.
     *
     * @return array|false
     */
    #[\DEPTRAC_202403\Relay\Attributes\RedisCommand]
    public function masters() : array|false
    {
    }
    /**
     * Returns the ID of the Sentinel instance.
     *
     * @return string
     */
    #[\DEPTRAC_202403\Relay\Attributes\RedisCommand]
    public function myid() : string
    {
    }
    /**
     * Returns PONG if no message is provided, otherwise returns the message.
     *
     * @param  string|null  $message
     * @return string|bool
     */
    #[\DEPTRAC_202403\Relay\Attributes\RedisCommand]
    public function ping(?string $message = null) : string|bool
    {
    }
    /**
     * Will reset all the masters with matching name.
     *
     * @param  string  $pattern
     * @return int
     */
    #[\DEPTRAC_202403\Relay\Attributes\RedisCommand]
    public function reset(string $pattern) : int
    {
    }
    /**
     * Returns a list of sentinel instances for this master, and their state.
     *
     * @param  string  $master
     * @return array|false
     */
    #[\DEPTRAC_202403\Relay\Attributes\RedisCommand]
    public function sentinels(string $master) : array|false
    {
    }
    /**
     * Show a list of replicas for this master, and their state.
     *
     * @param  string  $master
     * @return array|false
     */
    #[\DEPTRAC_202403\Relay\Attributes\RedisCommand]
    public function slaves(string $master) : array|false
    {
    }
    /**
     * Returns the last error message, if any.
     *
     * @return string|null
     */
    #[\DEPTRAC_202403\Relay\Attributes\Local]
    public function getLastError() : string|null
    {
    }
    /**
     * Sets a client option.
     *
     * @param  int  $option
     * @param  mixed  $value
     * @return bool
     */
    #[\DEPTRAC_202403\Relay\Attributes\Local]
    public function setOption(int $option, mixed $value) : bool
    {
    }
    /**
     * Returns a client option.
     *
     * @param  int  $option
     * @return mixed
     */
    #[\DEPTRAC_202403\Relay\Attributes\Local]
    public function getOption(int $option) : mixed
    {
    }
}
