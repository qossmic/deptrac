<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\ConfigurationRuleset;

final class ConfigurationRulesetTest extends TestCase
{
    public function testFromArray(): void
    {
        $configurationRuleSet = ConfigurationRuleset::fromArray(
           ['foo' => ['bar'], 'lala' => ['xx', 'yy']]
        );

        self::assertEquals(['bar'], $configurationRuleSet->getAllowedDependencies('foo'));
        self::assertEquals(['xx', 'yy'], $configurationRuleSet->getAllowedDependencies('lala'));
        self::assertEquals([], $configurationRuleSet->getAllowedDependencies('lalax'));
    }
}
