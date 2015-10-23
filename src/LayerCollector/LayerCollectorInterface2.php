<?php

namespace DependencyTracker\LayerCollector;

use DependencyTracker\CollectionMap;

interface LayerCollectorInterface
{
    public function supports($klass, $depdendency);

    public function handle(CollectionMap $map, $klass, $depdendency);

    public function getColor();
}