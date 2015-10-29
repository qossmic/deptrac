<?php

namespace DependencyTracker\DependencyResult;

class InheritDependency extends Dependency
{

    public static function fromDependency($inheritedByClass, $inheritedByLine, Dependency $dependency)
    {
        return new static($dependency->getClassA(), $dependency->getClassALine(), $dependency->getClassB(), $inheritedByClass, $inheritedByLine);
    }

}
