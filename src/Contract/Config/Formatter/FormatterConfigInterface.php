<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Contract\Config\Formatter;

interface FormatterConfigInterface
{
    public function getName() : string;
    /** @return array<mixed> */
    public function toArray() : array;
}
