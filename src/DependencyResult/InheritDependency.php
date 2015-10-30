<?php

namespace DependencyTracker\DependencyResult;

class InheritDependency extends Dependency
{

    public static function fromDependency($inheritedByClass, $inheritedByLine, Dependency $dependency)
    {
        return new static($inheritedByClass, $inheritedByLine, $dependency->getClassB(), $dependency->getClassA(), $dependency->getClassALine());
    }

}
