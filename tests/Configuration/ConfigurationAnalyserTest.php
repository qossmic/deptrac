<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\ConfigurationAnalyser;

/**
 * @covers \Qossmic\Deptrac\Configuration\ConfigurationAnalyser
 */
final class ConfigurationAnalyserTest extends TestCase
{
    public function testCountingAddsUseType(): void
    {
        $configuration = ConfigurationAnalyser::fromArray([
            'count_use_statements' => true,
        ]);

        self::assertSame([ConfigurationAnalyser::CLASS_TOKEN, ConfigurationAnalyser::USE_TOKEN], $configuration->getTypes());
    }

    public function testNotCounting(): void
    {
        $configuration = ConfigurationAnalyser::fromArray([
            'count_use_statements' => false,
        ]);

        self::assertSame([ConfigurationAnalyser::CLASS_TOKEN], $configuration->getTypes());
    }

    public function testDefaultTypes(): void
    {
        $configuration = ConfigurationAnalyser::fromArray([]);
        self::assertSame([ConfigurationAnalyser::CLASS_TOKEN, ConfigurationAnalyser::USE_TOKEN], $configuration->getTypes());
    }

    public function testCustomTypesWithAddedUse(): void
    {
        $types = [
            ConfigurationAnalyser::CLASS_TOKEN,
            ConfigurationAnalyser::FUNCTION_TOKEN,
        ];
        $configuration = ConfigurationAnalyser::fromArray(['count_use_statements' => true, 'types' => $types]);
        $types[] = ConfigurationAnalyser::USE_TOKEN;
        self::assertSame($types, $configuration->getTypes());
    }

    public function testCustomTypesWithDefaultUse(): void
    {
        $types = [
            ConfigurationAnalyser::CLASS_TOKEN,
            ConfigurationAnalyser::FUNCTION_TOKEN,
        ];
        $configuration = ConfigurationAnalyser::fromArray(['types' => $types]);
        self::assertSame($types, $configuration->getTypes());
    }

    public function testUnknownTypes(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ConfigurationAnalyser::fromArray([
            'types' => [
                'unknown',
            ],
        ]);
    }
}
