<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\Configuration;

class ConfigurationTest extends TestCase
{
    public function testFromArray(): void
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

        static::assertCount(2, $configuration->getLayers());
        static::assertEquals('some_name', $configuration->getLayers()[0]->getName());
        static::assertEquals(['foo', 'bar'], $configuration->getPaths());
        static::assertEquals(['foo2', 'bar2'], $configuration->getExcludeFiles());
        static::assertEquals(['xx', 'yy'], $configuration->getRuleset()->getAllowedDependencies('lala'));
    }

    public function testExcludedFilesAreOptional(): void
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

        static::assertSame([], $configuration->getExcludeFiles());
    }

    public function testFromWithNullExcludeFiles(): void
    {
        $configuration = Configuration::fromArray([
            'layers' => [],
            'paths' => [],
            'exclude_files' => null,
            'ruleset' => [],
        ]);

        static::assertEquals([], $configuration->getExcludeFiles());
    }
}
