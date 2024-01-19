<?php

declare (strict_types=1);
namespace DEPTRAC_202401\SimpleKafkaClient;

class Message
{
    public int $err;
    public string $topic_name;
    public int $timestamp;
    public int $partition;
    public int $payload;
    public int $len;
    public string $key;
    public int $offset;
    /**
     * @var array<string, mixed>
     */
    public array $headers;
    /**
     * @return string
     */
    public function getErrorString() : string
    {
    }
}
