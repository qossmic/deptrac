<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
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
        $this->assertTrue($configuration->isViolationSkipped('ClassWithOneDep', 'DependencyClass'));
        $this->assertFalse($configuration->isViolationSkipped('ClassWithEmptyDeps', 'DependencyClass'));
        $this->assertTrue($configuration->isViolationSkipped('ClassWithMultipleDeps', 'DependencyClass1'));
        $this->assertTrue($configuration->isViolationSkipped('ClassWithMultipleDeps', 'DependencyClass2'));
    }

    public function testFromArrayWithEmptyArrayAcceptable()
    {
        $configuration = ConfigurationSkippedViolation::fromArray([]);
        $this->assertFalse($configuration->isViolationSkipped('AnyClass', 'AnotherAnyClass'));
    }

    public function testFromArrayRequireOneArgument()
    {
        $this->expectException(\TypeError::class);
        ConfigurationSkippedViolation::fromArray();
    }
}
