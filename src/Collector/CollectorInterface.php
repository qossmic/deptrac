<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;

interface CollectorInterface
{
    /**
     * @return string used as an identifier to access to the collector or to display something more user-friendly
     *                name to the user when referring to the collector
     *
     * @example
     *  'bool', 'className', etc.
     */
    public function getType(): string;

    /**
     * @param array $configuration List of arguments passed for this collector declaration
     */
    public function satisfy(
        array $configuration,
        AstClassReference $astClassReference,
        AstMap $astMap,
        Registry $collectorRegistry
    ): bool;
}
