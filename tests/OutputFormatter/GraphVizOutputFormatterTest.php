<?php

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\OutputFormatter\GraphVizOutputFormatter;

class GraphVizOutputFormatterTest extends TestCase
{
    public function testGetName()
    {
        $this->assertEquals('graphviz', (new GraphVizOutputFormatter())->getName());
    }
}
