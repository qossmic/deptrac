<?php

namespace DependencyTracker\OutputFormatter;

use DependencyTracker\DependencyResult;

interface OutputFormatterInterface
{
    public function getName();

    public function finish(DependencyResult $dependencyResult);
}