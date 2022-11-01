<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Config;

use Stringable;

final class FormatterConfig implements Stringable
{
    public function __toString(): string
    {
        return 'formatter';
    }
}
