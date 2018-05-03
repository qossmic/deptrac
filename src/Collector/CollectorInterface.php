<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;

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
     * @param array                      $configuration          List of arguments passed for this collector declaration
     * @param AstClassReferenceInterface $abstractClassReference
     * @param AstMap                     $astMap
     * @param AstParserInterface         $astParser
     *
     * @return bool
     */
    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        AstMap $astMap,
        AstParserInterface $astParser
    ): bool;
}
