<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\OutputFormatter\Configuration\ConfigurationGraphViz;

/**
 * @covers \Qossmic\Deptrac\OutputFormatter\Configuration\ConfigurationGraphViz
 */
final class ConfigurationGraphVizTest extends TestCase
{
    public function testFromArray(): void
    {
        $hiddenLayers = ['hidden'];
        $groups = [
            'groupName' => [
                'layer1',
                'layer2',
            ],
        ];
        $arr = [
            'hidden_layers' => $hiddenLayers,
            'groups' => $groups,
        ];
        $configurationGraphViz = ConfigurationGraphViz::fromArray($arr);

        self::assertSame($hiddenLayers, $configurationGraphViz->getHiddenLayers());
        self::assertSame($groups, $configurationGraphViz->getGroupsLayerMap());
    }
}
