<?php

namespace DependencyTracker;


class CollectionMap
{
    protected $depdendencyMap = [];

    protected function normalize($name)
    {
        $name = str_replace(['\\', '//'], ['/', '/'], $name);

        $e = explode('/', $name);

        if (isset($e[1])) {
            return $e[0].'/'.$e[1];
        }

        return null;
    }

    public function addDependency($klass, $depdendency)
    {
        if ((stripos($klass, 'test') || stripos($depdendency, 'test'))) {
            return;
        }

        $klass = $this->normalize($klass);
        $depdendency = $this->normalize($depdendency);

        if (!$klass || !$depdendency) {
            return;
        }

        if (!isset($this->depdendencyMap[$klass])) {
            $this->depdendencyMap[$klass] = [];
        }

        $this->depdendencyMap[$klass][$depdendency] = $depdendency;
    }

    public function getDependencies()
    {
        return $this->depdendencyMap;
    }
} 