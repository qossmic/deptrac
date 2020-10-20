<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\Configuration\Configuration;
use SensioLabs\Deptrac\Configuration\Exception;

/**
 * @covers \SensioLabs\Deptrac\Configuration\Configuration
 */
class ConfigurationTest extends TestCase
{
    public function testFromArrayRejectsLayersWithDuplicateNames(): void
    {
        $this->expectException(Exception\InvalidConfigurationException::class);
        $this->expectExceptionMessage('Configuration can not contain multiple layers with the same name, got "baz", "foo" as duplicate.');

        Configuration::fromArray([
            'layers' => [
                [
                   'name' => 'foo',
                   'collectors' => [],
                ],
                [
                   'name' => 'foo',
                   'collectors' => [],
                ],
                [
                   'name' => 'bar',
                   'collectors' => [],
                ],
                [
                   'name' => 'baz',
                   'collectors' => [],
                ],
                [
                   'name' => 'baz',
                   'collectors' => [],
                ],
            ],
            'paths' => [
                'src',
            ],
            'ruleset' => [
                'foo' => [
                    'bar',
                ],
                'bar' => null,
                'baz' => [
                    'bar',
                ],
            ],
        ]);
    }

    public function testFromArray(): void
    {
        $configuration = Configuration::fromArray([
            'layers' => [
                [
                   'name' => 'some_name',
                   'collectors' => [],
                ],
                [
                   'name' => 'some_other_name',
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
        static::assertTrue($configuration->ignoreUncoveredInternalClasses());
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
                   'name' => 'some_other_name',
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

    public function testSkipViolations(): void
    {
        $configuration = Configuration::fromArray([
            'layers' => [],
            'paths' => [],
            'exclude_files' => null,
            'ruleset' => [],
            'skip_violations' => [
                'FooClass' => [
                    'BarClass',
                    'AnotherClass',
                ],
            ],
        ]);

        static::assertTrue($configuration->getSkipViolations()->isViolationSkipped(ClassLikeName::fromFQCN('FooClass'), ClassLikeName::fromFQCN('BarClass')));
        static::assertTrue($configuration->getSkipViolations()->isViolationSkipped(ClassLikeName::fromFQCN('FooClass'), ClassLikeName::fromFQCN('AnotherClass')));
    }

    public function testIgnoreUncoveredInternalClassesSetToFalse(): void
    {
        $configuration = Configuration::fromArray([
            'layers' => [],
            'paths' => [],
            'ruleset' => [],
            'ignore_uncovered_internal_classes' => false,
        ]);

        static::assertFalse($configuration->ignoreUncoveredInternalClasses());
    }
}
