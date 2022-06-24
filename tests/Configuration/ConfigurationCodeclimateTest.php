<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\OutputFormatter\Configuration\ConfigurationCodeclimate;

/**
 * @covers \Qossmic\Deptrac\OutputFormatter\Configuration\ConfigurationCodeclimate
 */
final class ConfigurationCodeclimateTest extends TestCase
{
    public function testFromArray(): void
    {
        $arr = [
            'severity' => [
                'failure' => 'blocker',
                'skipped' => 'critical',
                'uncovered' => 'info',
            ],
        ];
        $config = ConfigurationCodeclimate::fromArray($arr);

        self::assertSame('blocker', $config->getSeverity('failure'));
        self::assertSame('critical', $config->getSeverity('skipped'));
        self::assertSame('info', $config->getSeverity('uncovered'));
    }
}
