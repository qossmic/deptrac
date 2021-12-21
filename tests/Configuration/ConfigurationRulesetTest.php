<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Configuration;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\ConfigurationRuleset;

final class ConfigurationRulesetTest extends TestCase
{
    public function testFromArray(): void
    {
        $configurationRuleSet = ConfigurationRuleset::fromArray(
           ['foo' => ['bar'], 'lala' => ['xx', 'yy']], [], false
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
        ], [], false);

        self::assertEquals(['baz', 'bar'], $configurationRuleSet->getAllowedDependencies('foo'));
        self::assertEquals(['corge', 'quuz', 'baz', 'bar', 'foo', 'grault'], $configurationRuleSet->getAllowedDependencies('qux'));
    }

    public function testFromArrayTransitiveCircular(): void
    {
        $configurationRuleSet = ConfigurationRuleset::fromArray([
            'a' => ['+b'],
            'b' => ['+c'],
            'c' => ['+a'],
        ], [], false);
        $this->expectException(InvalidArgumentException::class);
        $configurationRuleSet->getAllowedDependencies('a');
    }
}
