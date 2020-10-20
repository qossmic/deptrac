<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\Configuration\ConfigurationSkippedViolation;

final class ConfigurationSkippedViolationTest extends TestCase
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
        static::assertTrue($configuration->isViolationSkipped(ClassLikeName::fromFQCN('ClassWithOneDep'), ClassLikeName::fromFQCN('DependencyClass')));
        static::assertFalse($configuration->isViolationSkipped(ClassLikeName::fromFQCN('ClassWithEmptyDeps'), ClassLikeName::fromFQCN('DependencyClass')));
        static::assertTrue($configuration->isViolationSkipped(ClassLikeName::fromFQCN('ClassWithMultipleDeps'), ClassLikeName::fromFQCN('DependencyClass1')));
        static::assertTrue($configuration->isViolationSkipped(ClassLikeName::fromFQCN('ClassWithMultipleDeps'), ClassLikeName::fromFQCN('DependencyClass2')));
    }

    public function testFromArrayWithEmptyArrayAcceptable(): void
    {
        $configuration = ConfigurationSkippedViolation::fromArray([]);
        static::assertFalse($configuration->isViolationSkipped(ClassLikeName::fromFQCN('AnyClass'), ClassLikeName::fromFQCN('AnotherAnyClass')));
    }

    public function testFromArrayRequireOneArgument(): void
    {
        $this->expectException(\TypeError::class);
        ConfigurationSkippedViolation::fromArray();
    }
}
