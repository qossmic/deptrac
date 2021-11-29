<?php

namespace Tests\Qossmic\Deptrac;

use SebastianBergmann\Comparator\Factory;
use staabm\PHPUnitCrossOs\Comparator\CrossOsAgnosticStringComparator;

trait CrossOsAgnosticEqualsTrait {
    /**
     * @var CrossOsAgnosticStringComparator
     */
    private $comparator;

    public function setUp(): void
    {
        // make assertEquals* comparisons PHP_EOL and DIRECTORY_SEPARATOR agnostic
        $this->comparator = new CrossOsAgnosticStringComparator();

        $factory = Factory::getInstance();
        $factory->register($this->comparator);
    }

    public function tearDown(): void
    {
        $factory = Factory::getInstance();
        $factory->unregister($this->comparator);
    }
}
