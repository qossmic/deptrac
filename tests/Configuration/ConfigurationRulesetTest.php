<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\ConfigurationRuleset;

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

    public function testFromArrayTransitive(): void
    {
        $configurationRuleSet = ConfigurationRuleset::fromArray([
            'foo' => ['+bar'],
            'bar' => ['baz'],
            'baz' => ['qux'],

            'qux' => ['+quuz', '+grault'],
            'quuz' => ['corge'],
            'grault' => ['+foo', 'baz'],
        ]);

        self::assertEquals(['baz', 'bar'], $configurationRuleSet->getAllowedDependencies('foo'));
        self::assertEquals(['corge', 'quuz', 'baz', 'bar', 'foo', 'grault'], $configurationRuleSet->getAllowedDependencies('qux'));
    }

    public function testFromArrayTransitiveCircular(): void
    {
        $configurationRuleSet = ConfigurationRuleset::fromArray([
            'a' => ['+b'],
            'b' => ['+c'],
            'c' => ['+a'],
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $configurationRuleSet->getAllowedDependencies('a');
    }
}
