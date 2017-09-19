<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\CollectorFactory;

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
     * @param AstClassReferenceInterface $abstractClassReference
     * @param AstMap                     $astMap
     * @param CollectorFactory           $collectorFactory
     * @param AstParserInterface         $astParser
     *
     * @return boolean
     */
    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        AstMap $astMap,
        CollectorFactory $collectorFactory,
        AstParserInterface $astParser
    );
}
