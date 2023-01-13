<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\InputCollector;

interface InputCollectorInterface
{
    /**
     * @return string[]
     *
     * @throws InputException
     */
    public function collect(): array;
}
