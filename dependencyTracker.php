<?php

require __DIR__.'/vendor/autoload.php';

use \Symfony\Component\Console\Application;

$application = new Application();
$application->add(new \DependencyTracker\Command\AnalyzeCommand());
$application->run();