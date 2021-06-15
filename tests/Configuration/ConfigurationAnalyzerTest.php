<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\ConfigurationAnalyzer;

/**
 * @covers ConfigurationAnalyzer
 */
final class ConfigurationAnalyzerTest extends TestCase
{
    public function testCounting(): void
    {
        $configuration = ConfigurationAnalyzer::fromArray([
            'count_use_statements' => true,
        ]);

        self::assertTrue($configuration->isCountingUseStatements());
    }

    public function testNotCounting(): void
    {
        $configuration = ConfigurationAnalyzer::fromArray([
            'count_use_statements' => false,
        ]);

        self::assertFalse($configuration->isCountingUseStatements());
    }

    public function testDefaultCounting(): void
    {
        $configuration = ConfigurationAnalyzer::fromArray([]);
        self::assertTrue($configuration->isCountingUseStatements());
    }
}
