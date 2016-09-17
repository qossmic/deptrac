<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\Deptrac\CollectorFactory;

/**
 * A collector is responsible to tell from an AST node (e.g. a specific class) is part of a layer.
 */
interface CollectorInterface
{
    /**
     * @return string Used as an identifier to access to the collector or to display something more user-friendly
     *                name to the user when referring to the collector.
     *
     * @example
     *  'bool', 'className', etc.
     */
    public function getType();

    /**
     * @param array                      $configuration List of arguments passed for this collector declaration
     * @param AstClassReferenceInterface $abstractClassReference Class being checked
     * @param AstMap                     $astMap
     * @param CollectorFactory           $collectorFactory
     * @param AstParserInterface         $astParser
     *
     * @return boolean
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
        AstClassReferenceInterface $abstractClassReference,
        AstMap $astMap,
        CollectorFactory $collectorFactory,
        AstParserInterface $astParser
    );
}
