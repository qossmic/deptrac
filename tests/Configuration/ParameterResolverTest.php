<?php

namespace Tests\Qossmic\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\ParameterResolver;

final class ParameterResolverTest extends TestCase
{
    /**
     * @dataProvider parametersProvider
     *
     * @param string|string[]       $value
     * @param array<string, string> $parameters
     */
    public function testResolveParameters($value, array $parameters, $expected): void
    {
        self::assertSame($expected, (new ParameterResolver())->resolve($value, $parameters));
    }

    public function parametersProvider(): iterable
    {
        yield [
            'value' => [],
            'parameters' => [
                'foo' => 'bar',
            ],
            'expected' => [],
        ];

        yield [
            'value' => ['%parameter%'],
            'parameters' => [],
            'expected' => ['%parameter%'],
        ];

        yield [
            'value' => ['%parameter%'],
            'parameters' => [
                'parameter' => 'value',
            ],
            'expected' => ['value'],
        ];

        yield [
            'value' => [
                'param' => '%parameter%\\param',
                'foo' => 'foo\\%parameter%\\param',
            ],
            'parameters' => [
                'parameter' => 'value',
            ],
            'expected' => [
                'param' => 'value\param',
                'foo' => 'foo\value\param',
            ],
        ];

        yield [
            'value' => [
                'param' => '%parameter%\\param',
                'must_not' => [
                    [
                        'type' => 'className',
                        'regex' => 'foo\\%parameter%\\param',
                    ],
                ],
            ],
            'parameters' => [
                'parameter' => 'value',
            ],
            'expected' => [
                'param' => 'value\param',
                'must_not' => [
                    [
                        'type' => 'className',
                        'regex' => 'foo\\value\\param',
                    ],
                ],
            ],
        ];
    }
}
