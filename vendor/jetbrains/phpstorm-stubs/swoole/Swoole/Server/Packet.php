<?php

declare (strict_types=1);
namespace DEPTRAC_202401\Swoole\Server;

class Packet
{
    public $server_socket = 0;
    public $server_port = 0;
    public $dispatch_time = 0;
    public $address;
    public $port = 0;
}
