<?php

namespace DependencyTracker\Collector;

use DependencyTracker\CollectorFactory;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;

interface CollectorInterface
{
    public function getType();

    public function satisfy(
        array $configuration,
        AstClassReferenceInterface $abstractClassReference,
        CollectorFactory $collectorFactory
    );
}