<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Configuration;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Configuration\Definition;
use Symfony\Component\Config\Definition\Processor;

class DefinitionTest extends TestCase
{
    public function testConfig(): void
    {
        $configs = [
            [
                'paths' => ['test/'],
                'layers' => [
                    [
                        'name' => 'Test',
                        'collectors' => [
                            [
                                'type' => 'test',
                                'regex' => 'Test*',
                            ],
                        ],
                    ],
                ],
                'ruleset' => [
                ],
                'skip_violations' => [
                    'SomeClass' => [
                        'OtherClass',
                    ],
                ],
            ],
            [
                'layers' => [
                    [
                        'name' => 'Test',
                        'collectors' => [
                            [
                                'type' => 'directory',
                                'regex' => 'src/Test/',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Test 2',
                        'collectors' => [
                            [
                                'type' => 'test2',
                                'regex' => 'Test2*',
                            ],
                        ],
                    ],
                ],
                'ruleset' => [
                    'Test' => [
                        'Test 2',
                    ],
                ],
                'skip_violations' => [
                    'SomeClass' => [
                        'FooBar',
                    ],
                ],
            ],
        ];

        $configuration = (new Processor())->processConfiguration(new Definition(), $configs);

        self::assertSame([
            'paths' => ['test/'],
            'layers' => [
                'Test' => [
                    'name' => 'Test',
                    'collectors' => [
                        [
                            'type' => 'test',
                            'regex' => 'Test*',
                        ],
                        [
                            'type' => 'directory',
                            'regex' => 'src/Test/',
                        ],
                    ],
                ],
                'Test 2' => [
                    'name' => 'Test 2',
                    'collectors' => [
                        [
                            'type' => 'test2',
                            'regex' => 'Test2*',
                        ],
                    ],
                ],
            ],
            'ruleset' => [
                'Test' => [
                    'Test 2',
                ],
            ],
            'skip_violations' => [
                'SomeClass' => [
                    'OtherClass',
                    'FooBar',
                ],
            ],
            'imports' => [],
            'exclude_files' => [],
            'ignore_uncovered_internal_classes' => true,
            'use_relative_path_from_depfile' => true,
        ], $configuration);
    }
}
