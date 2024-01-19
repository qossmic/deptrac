<?php

declare (strict_types=1);
namespace DEPTRAC_202401\Swoole\Http2;

class Response
{
    public $streamId = 0;
    public $errCode = 0;
    public $statusCode = 0;
    public $pipeline = \false;
    public $headers;
    public $set_cookie_headers;
    public $cookies;
    public $data;
}
