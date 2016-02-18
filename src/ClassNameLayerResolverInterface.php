<?php

namespace DependencyTracker;

interface ClassNameLayerResolverInterface
{
    public function getLayersByClassName($className);
}
