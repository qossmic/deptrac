<?php

namespace SensioLabs\Deptrac\Configuration;

use SensioLabs\Deptrac\Collector\CollectorInterface;

interface ConfigurationLayerInterface
{

    /** @return CollectorInterface */
    public function getCollectors();

    /** @return string */
    public function getName();

    /** @return string */
    public function getPathname();

    /** @return ConfigurationLayerInterface[] */
    public function getLayers();

    /** @return int */
    public function getId();

}