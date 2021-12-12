<?php

namespace Tests\Qossmic\Deptrac;

use SebastianBergmann\Comparator\Factory;
use staabm\PHPUnitCrossOs\Comparator\CrossOsAgnosticComparator;

trait CrossOsAgnosticEqualsTrait {
    /**
     * @var CrossOsAgnosticComparator
     */
    private $comparator;

    public function setUp(): void
    {
        // make assert* comparisons support EolAgnosticString and DirSeparatorAgnosticString aware
        $this->comparator = new CrossOsAgnosticComparator();

        $factory = Factory::getInstance();
        $factory->register($this->comparator);
    }

    public function tearDown(): void
    {
        $factory = Factory::getInstance();
        $factory->unregister($this->comparator);
    }
}
