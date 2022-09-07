<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Supportive\OutputFormatter\Configuration\ConfigurationGraphViz;

/**
 * @covers \Qossmic\Deptrac\Supportive\OutputFormatter\Configuration\ConfigurationGraphViz
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
        $pointToGroups = true;
        $arr = [
            'hidden_layers' => $hiddenLayers,
            'groups' => $groups,
            'point_to_groups' => $pointToGroups,
        ];
        $configurationGraphViz = ConfigurationGraphViz::fromArray($arr);

        self::assertSame($hiddenLayers, $configurationGraphViz->hiddenLayers);
        self::assertSame($groups, $configurationGraphViz->groupsLayerMap);
        self::assertSame($pointToGroups, $configurationGraphViz->pointToGroups);
    }
}
