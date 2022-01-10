<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use Qossmic\Deptrac\AstRunner\AstMap;

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
     * @param AstMap\AstTokenReference    $astTokenReference Token being checked
     * @param array<string, ?bool>        $resolutionTable   layer name => is part of the layer? (NULL = Unknown)
     *
     * @example
     *  For the YAML configuration:
     *  ```yaml
     *  collectors:
     *      - type: className
     *        regex: .*Controller.*
     *  ```
     *  The configuration `$configuration` will be:
     *  [
     *      'type' => 'className',
     *      'regex' => '.*Controller.*',
     *  ]
     */
    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        array $resolutionTable = []
    ): bool;

    /**
     * @param array<string, ?bool> $resolutionTable layer name => is part of the layer? (NULL = Unknown)
     */
    public function resolvable(array $configuration, Registry $collectorRegistry, array $resolutionTable): bool;
}
