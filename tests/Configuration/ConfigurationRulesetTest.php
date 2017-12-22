<?php

namespace Tests\SensioLabs\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Configuration\ConfigurationRuleset;

class ConfigurationRulesetTest extends TestCase
{
    public function testFromArray()
    {
        $configurationRuleSet = ConfigurationRuleset::fromArray(
           ['foo' => ['bar'], 'lala' => ['xx', 'yy']]
        );

        $this->assertEquals(['bar'], $configurationRuleSet->getAllowedDependendencies('foo'));
        $this->assertEquals(['xx', 'yy'], $configurationRuleSet->getAllowedDependendencies('lala'));
        $this->assertEquals([], $configurationRuleSet->getAllowedDependendencies('lalax'));
    }
}
