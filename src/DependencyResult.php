<?php 

namespace DependencyTracker;

use DependencyTracker\DependencyResult\Dependency;

class DependencyResult
{

    private $classLayerMap = [];

    private $dependencies = [];

    public function addDependency(Dependency $dependency)
    {
        $this->dependencies[] = $dependency;
    }

    /** @return Dependency[] */
    public function getDependencies()
    {
        return $this->dependencies;
    }

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
