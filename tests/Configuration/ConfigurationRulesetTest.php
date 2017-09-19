<?php

namespace Tests\SensioLabs\Deptrac\Configuration;

use SensioLabs\Deptrac\Configuration\ConfigurationRuleset;

class ConfigurationRulesetTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArray()
    {
        $configurationRuleSet = ConfigurationRuleset::fromArray(
           ['foo' => ['bar'], 'lala' => ['xx', 'yy']]
        );

        $this->assertEquals(['bar'], $configurationRuleSet->getAllowedDependencies('foo'));
        $this->assertEquals(['xx', 'yy'], $configurationRuleSet->getAllowedDependencies('lala'));
        $this->assertEquals([], $configurationRuleSet->getAllowedDependencies('lalax'));
    }
}
