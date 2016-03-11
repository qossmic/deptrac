<?php

namespace SensioLabs\Deptrac\Tests\OutputFormatter;

use SensioLabs\Deptrac\OutputFormatter\GraphVizOutputFormatter;

class GraphVizOutputFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $this->assertEquals('graphviz', (new GraphVizOutputFormatter())->getName());
    }
}
