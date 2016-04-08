<?php

namespace SensioLabs\Deptrac\Tests\RuleEngine;

use SensioLabs\Deptrac\DependencyResult\DependencyInterface;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;

class RulesetViolationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $ruleViolation = new RulesetViolation(
            $dep = $this->prophesize(DependencyInterface::class)->reveal(),
            'layerA',
            'layerB'
        );

        $this->assertSame($dep, $ruleViolation->getDependency());
        $this->assertEquals('layerA', $ruleViolation->getLayerA());
        $this->assertEquals('layerB', $ruleViolation->getLayerB());
    }
}
