<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Config\Formatter;

interface FormatterConfigInterface
{
    public function getName(): string;

    public function toArray(): array;
}
