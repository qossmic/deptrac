<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\RuleEngine;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Dependency\DependencyInterface;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;

class RulesetViolationTest extends TestCase
{
    public function testGetSet(): void
    {
        $ruleViolation = new RulesetViolation(
            $dep = $this->prophesize(DependencyInterface::class)->reveal(),
            'layerA',
            'layerB'
        );

        static::assertSame($dep, $ruleViolation->getDependency());
        static::assertEquals('layerA', $ruleViolation->getLayerA());
        static::assertEquals('layerB', $ruleViolation->getLayerB());
    }
}
