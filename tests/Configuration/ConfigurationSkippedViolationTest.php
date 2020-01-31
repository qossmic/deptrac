<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\Configuration\ConfigurationSkippedViolation;

class ConfigurationSkippedViolationTest extends TestCase
{
    public function testFromArray(): void
    {
        $configuration = ConfigurationSkippedViolation::fromArray([
            'ClassWithOneDep' => [
                'DependencyClass',
            ],
            'ClassWithEmptyDeps' => [],
            'ClassWithMultipleDeps' => [
                'DependencyClass1',
                'DependencyClass2',
                'DependencyClass2',
            ],
        ]);
        static::assertTrue($configuration->isViolationSkipped(ClassLikeName::fromString('ClassWithOneDep'), ClassLikeName::fromString('DependencyClass')));
        static::assertFalse($configuration->isViolationSkipped(ClassLikeName::fromString('ClassWithEmptyDeps'), ClassLikeName::fromString('DependencyClass')));
        static::assertTrue($configuration->isViolationSkipped(ClassLikeName::fromString('ClassWithMultipleDeps'), ClassLikeName::fromString('DependencyClass1')));
        static::assertTrue($configuration->isViolationSkipped(ClassLikeName::fromString('ClassWithMultipleDeps'), ClassLikeName::fromString('DependencyClass2')));
    }

    public function testFromArrayWithEmptyArrayAcceptable(): void
    {
        $configuration = ConfigurationSkippedViolation::fromArray([]);
        static::assertFalse($configuration->isViolationSkipped(ClassLikeName::fromString('AnyClass'), ClassLikeName::fromString('AnotherAnyClass')));
    }

    public function testFromArrayRequireOneArgument(): void
    {
        $this->expectException(\TypeError::class);
        ConfigurationSkippedViolation::fromArray();
    }
}
