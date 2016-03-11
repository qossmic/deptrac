<?php

namespace SensioLabs\Deptrac\Tests;

use SensioLabs\Deptrac\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArray()
    {
        $configuration = Configuration::fromArray([
            'layers' => [
                [
                   'name' => 'some_name',
                   'collectors' => [],
                ],
                [
                   'name' => 'some_name',
                   'collectors' => [],
                ],
            ],
            'paths' => [
                'foo',
                'bar',
            ],
            'exclude_files' => [
                'foo2',
                'bar2',
            ],
            'ruleset' => [
                'lala' => ['xx', 'yy'],
            ],
        ]);

        $this->assertCount(2, $configuration->getLayers());
        $this->assertEquals('some_name', $configuration->getLayers()[0]->getName());
        $this->assertEquals(['foo', 'bar'], $configuration->getPaths());
        $this->assertEquals(['foo2', 'bar2'], $configuration->getExcludeFiles());
        $this->assertEquals('graphviz, console', $configuration->getFormatter());
        $this->assertEquals(['xx', 'yy'], $configuration->getRuleset()->getAllowedDependendencies('lala'));
    }
}
