<?php

declare (strict_types=1);
namespace DEPTRAC_202401\SimpleKafkaClient;

class Metadata
{
    /**
     * @return int
     * @throws Exception
     */
    public function getOrigBrokerId() : int
    {
    }
    /**
     * @return string
     * @throws Exception
     */
    public function getOrigBrokerName() : string
    {
    }
    /**
     * @return Metadata\Collection
     * @throws Exception
     */
    public function getBrokers() : Metadata\Collection
    {
    }
    /**
     * @return Metadata\Collection
     * @throws Exception
     */
    public function getTopics() : Metadata\Collection
    {
    }
}
