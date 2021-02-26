<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;

/**
 * A collector is responsible to tell from an AST node (e.g. a specific class) is part of a layer.
 */
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
     * @param array<string, string|array> $configuration     List of arguments passed for this collector declaration
     * @param AstClassReference           $astClassReference Class being checked
     *
     * @example
     *  For the YAML configuration:
     *
     *  ```yaml
     *  collectors:
     *      - type: className
     *        regex: .*Controller.*
     *  ```
     *
     *  The configuration `$configuration` will be:
     *  [
     *      'type' => 'className',
     *      'regex' => '.*Controller.*',
     *  ]
     */
    public function satisfy(
        array $configuration,
        AstClassReference $astClassReference,
        AstMap $astMap,
        Registry $collectorRegistry
    ): bool;
}
