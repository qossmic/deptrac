<?php

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\Configuration;

class ConfigurationTest extends TestCase
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
        $this->assertEquals(['xx', 'yy'], $configuration->getRuleset()->getAllowedDependencies('lala'));
    }

    public function testExludedFilesAreOptional()
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
            'ruleset' => [
                'lala' => ['xx', 'yy'],
            ],
        ]);

        $this->assertSame([], $configuration->getExcludeFiles());
    }

    public function testFromWithNullExcludeFiles()
    {
        $configuration = Configuration::fromArray([
            'layers' => [],
            'paths' => [],
            'exclude_files' => null,
            'ruleset' => [],
        ]);

        $this->assertEquals([], $configuration->getExcludeFiles());
    }
}
