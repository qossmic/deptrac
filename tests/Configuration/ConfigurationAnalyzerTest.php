<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\ConfigurationAnalyzer;

/**
 * @covers \Qossmic\Deptrac\Configuration\ConfigurationAnalyzer
 */
final class ConfigurationAnalyzerTest extends TestCase
{
    public function testCountingAddsUseType(): void
    {
        $configuration = ConfigurationAnalyzer::fromArray([
            'count_use_statements' => true,
        ]);

        self::assertSame([ConfigurationAnalyzer::CLASS_TOKEN, ConfigurationAnalyzer::USE_TOKEN], $configuration->getTypes());
    }

    public function testNotCounting(): void
    {
        $configuration = ConfigurationAnalyzer::fromArray([
            'count_use_statements' => false,
        ]);

        self::assertSame([ConfigurationAnalyzer::CLASS_TOKEN], $configuration->getTypes());
    }

    public function testDefaultTypes(): void
    {
        $configuration = ConfigurationAnalyzer::fromArray([]);
        self::assertSame([ConfigurationAnalyzer::CLASS_TOKEN, ConfigurationAnalyzer::USE_TOKEN], $configuration->getTypes());
    }

    public function testCustomTypes(): void
    {
        $types = [
            ConfigurationAnalyzer::CLASS_TOKEN,
            ConfigurationAnalyzer::FUNCTION_TOKEN,
        ];
        $configuration = ConfigurationAnalyzer::fromArray(['types' => $types]);
        $types[] = ConfigurationAnalyzer::USE_TOKEN;
        self::assertSame($types, $configuration->getTypes());
    }

    public function testUnknownTypes(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ConfigurationAnalyzer::fromArray([
            'types' => [
                'unknown',
            ],
        ]);
    }
}
