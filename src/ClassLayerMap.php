<?php

namespace DependencyTracker;

class ClassLayerMap
{
    private $classLayerMap = [];

    public function addClassToLayer($klass, $layer)
    {
        if(!isset($this->classLayerMap[$klass])) {
            $this->classLayerMap[$klass] = [];
        }

        $this->classLayerMap[$klass][] = $layer;
    }

    public function getClassLayerMap()
    {
        return $this->classLayerMap;
    }

    public function getLayersByClassName($className)
    {
        if (!isset($this->classLayerMap[$className])) {
            return [];
        }

        return $this->classLayerMap[$className];
    }

}
