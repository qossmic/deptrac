<?php

namespace DependencyTracker\Collector;

use DependencyTracker\CollectorFactory;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;

interface CollectorInterface
{
    public function getType();

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        AstMap $astMap,
        CollectorFactory $collectorFactory
    );
}