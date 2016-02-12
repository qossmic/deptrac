<?php


namespace DependencyTracker\Tests\Configuration;


use DependencyTracker\Configuration\ConfigurationRuleset;

class ConfigurationRulesetTest extends \PHPUnit_Framework_TestCase
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
