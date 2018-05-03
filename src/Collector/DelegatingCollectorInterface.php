<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Collector;

interface DelegatingCollectorInterface
{
    public function setRegistry(Registry $registry);

    public function getRegistry(): Registry;
}
