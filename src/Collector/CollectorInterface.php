<?php

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\Deptrac\CollectorFactory;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;

interface CollectorInterface
{
    public function getType();

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        AstMap $astMap,
        CollectorFactory $collectorFactory,
        AstParserInterface $astParser
    );
}
