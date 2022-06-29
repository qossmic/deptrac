<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\InputCollector;

interface InputCollectorInterface
{
    /**
     * @return string[]
     */
    public function collect(): array;
}
