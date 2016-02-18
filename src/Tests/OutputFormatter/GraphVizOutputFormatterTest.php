<?php


namespace DependencyTracker\Tests\OutputFormatter;


use DependencyTracker\OutputFormatter\GraphVizOutputFormatter;

class GraphVizOutputFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $this->assertEquals('graphviz', (new GraphVizOutputFormatter())->getName());
    }
}
