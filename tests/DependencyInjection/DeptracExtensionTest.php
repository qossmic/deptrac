<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\DependencyInjection;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Dependency\Emitter\EmitterTypes;
use Qossmic\Deptrac\DependencyInjection\DeptracExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DeptracExtensionTest extends TestCase
{
    private ContainerBuilder $container;
    private DeptracExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new ContainerBuilder();
        $this->extension = new DeptracExtension();
    }

    public function testDefaults(): void
    {
        $configs = [];

        $this->extension->load($configs, $this->container);

        self::assertSame(['src/'], $this->container->getParameter('paths'));
        self::assertSame([], $this->container->getParameter('exclude_files'));
        self::assertSame([], $this->container->getParameter('layers'));
        self::assertSame([], $this->container->getParameter('ruleset'));
        self::assertSame([], $this->container->getParameter('skip_violations'));
        self::assertSame([], $this->container->getParameter('formatters'));
        self::assertSame(['types' => [EmitterTypes::CLASS_TOKEN, EmitterTypes::USE_TOKEN]], $this->container->getParameter('analyser'));
        self::assertSame(true, $this->container->getParameter('ignore_uncovered_internal_classes'));
        self::assertSame(true, $this->container->getParameter('use_relative_path_from_depfile'));
    }

    public function testDefaultsWithEmptyRoot(): void
    {
        $configs = [
            'deptrac' => [],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(['src/'], $this->container->getParameter('paths'));
        self::assertSame([], $this->container->getParameter('exclude_files'));
        self::assertSame([], $this->container->getParameter('layers'));
        self::assertSame([], $this->container->getParameter('ruleset'));
        self::assertSame([], $this->container->getParameter('skip_violations'));
        self::assertSame([], $this->container->getParameter('formatters'));
        self::assertSame(['types' => [EmitterTypes::CLASS_TOKEN, EmitterTypes::USE_TOKEN]], $this->container->getParameter('analyser'));
        self::assertSame(true, $this->container->getParameter('ignore_uncovered_internal_classes'));
        self::assertSame(true, $this->container->getParameter('use_relative_path_from_depfile'));
    }

    public function testPathsWithMultipleElements(): void
    {
        $configs = [
            'deptrac' => [
                'paths' => ['tests/', 'src/', 'lib/'],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(['tests/', 'src/', 'lib/'], $this->container->getParameter('paths'));
    }

    public function testPathsWithSingleElement(): void
    {
        $configs = [
            'deptrac' => [
                'paths' => ['lib/'],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(['lib/'], $this->container->getParameter('paths'));
    }

    public function testPathsWithScalar(): void
    {
        $configs = [
            'deptrac' => [
                'path' => 'tests/',
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(['tests/'], $this->container->getParameter('paths'));
    }

    public function testExcludePatternWithMultipleElements(): void
    {
        $configs = [
            'deptrac' => [
                'exclude_files' => ['.*', '/\d+/'],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(['.*', '/\d+/'], $this->container->getParameter('exclude_files'));
    }

    public function testExcludePatternsWithSingleElement(): void
    {
        $configs = [
            'deptrac' => [
                'exclude_files' => ['#.*Test\.php#'],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(['#.*Test\.php#'], $this->container->getParameter('exclude_files'));
    }

    public function testExcludePatternsWithScalar(): void
    {
        $configs = [
            'deptrac' => [
                'exclude_file' => '/^[a-z]+$/',
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(['/^[a-z]+$/'], $this->container->getParameter('exclude_files'));
    }

    public function testNullLayers(): void
    {
        $configs = [
            'deptrac' => [
                'layers' => null,
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame([], $this->container->getParameter('layers'));
    }

    public function testEmptyLayers(): void
    {
        $configs = [
            'deptrac' => [
                'layers' => [],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame([], $this->container->getParameter('layers'));
    }

    public function testLayers(): void
    {
        $configs = [
            'deptrac' => [
                'layers' => [
                    [
                        'name' => 'test',
                        'collectors' => [
                            [
                                'type' => 'directory',
                                'value' => 'Repository',
                                'attributes' => [],
                            ],
                        ],
                        'attributes' => [],
                    ],
                ],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(
            [
                'test' => [
                    'name' => 'test',
                    'collectors' => [
                        [
                            'type' => 'directory',
                            'value' => 'Repository',
                            'attributes' => [],
                        ],
                    ],
                    'attributes' => [],
                ],
            ],
            $this->container->getParameter('layers')
        );
    }

    public function testNullRuleset(): void
    {
        $configs = [
            'deptrac' => [
                'ruleset' => null,
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(
            [],
            $this->container->getParameter('ruleset')
        );
    }

    public function testEmptyRuleset(): void
    {
        $configs = [
            'deptrac' => [
                'ruleset' => [],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(
            [],
            $this->container->getParameter('ruleset')
        );
    }

    public function testRuleset(): void
    {
        $configs = [
            'deptrac' => [
                'ruleset' => [
                    'Foo' => ['Bar', 'Baz'],
                    'Bar' => null,
                    'Baz' => [],
                ],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(
            [
                'Foo' => ['Bar', 'Baz'],
                'Bar' => [],
                'Baz' => [],
            ],
            $this->container->getParameter('ruleset')
        );
    }

    public function testSkipViolations(): void
    {
        $configs = [
            'deptrac' => [
                'skip_violations' => [
                    'examples\Layer2\SomeOtherClass' => [
                        'examples\Layer1\SomeClass',
                    ],
                ],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(
            [
                'examples\Layer2\SomeOtherClass' => [
                    'examples\Layer1\SomeClass',
                ],
            ],
            $this->container->getParameter('skip_violations')
        );
    }

    public function testFormatters(): void
    {
        $configs = [
            'deptrac' => [
                'formatters' => [
                    'graphviz' => [
                        'hidden_layers' => ['Utils'],
                    ],
                ],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(
            [
                'graphviz' => [
                    'hidden_layers' => ['Utils'],
                ],
            ],
            $this->container->getParameter('formatters')
        );
    }

    public function testNullAnalyser(): void
    {
        $configs = [
            'deptrac' => [
                'analyser' => null,
            ],
        ];

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child config "types" under "deptrac.analyser" must be configured.');

        $this->extension->load($configs, $this->container);
    }

    public function testEmptyAnalyser(): void
    {
        $configs = [
            'deptrac' => [
                'analyser' => [],
            ],
        ];

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child config "types" under "deptrac.analyser" must be configured.');

        $this->extension->load($configs, $this->container);
    }

    public function testInvalidAnalyserTypes(): void
    {
        $configs = [
            'deptrac' => [
                'analyser' => [
                    'types' => ['invalid'],
                ],
            ],
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid type "invalid"');

        $this->extension->load($configs, $this->container);
    }

    public function testNullAnalyserTypes(): void
    {
        $configs = [
            'deptrac' => [
                'analyser' => [
                    'types' => null,
                ],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(
            ['types' => []],
            $this->container->getParameter('analyser')
        );
    }

    public function testEmptyAnalyserTypes(): void
    {
        $configs = [
            'deptrac' => [
                'analyser' => [
                    'types' => [],
                ],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(
            ['types' => []],
            $this->container->getParameter('analyser')
        );
    }

    public function testAnalyserWithDuplicateTypes(): void
    {
        $configs = [
            'deptrac' => [
                'analyser' => [
                    'types' => [EmitterTypes::CLASS_TOKEN, EmitterTypes::CLASS_TOKEN],
                ],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(
            ['types' => [EmitterTypes::CLASS_TOKEN, EmitterTypes::CLASS_TOKEN]],
            $this->container->getParameter('analyser')
        );
    }

    public function testIgnoreUncoveredInternalClasses(): void
    {
        $configs = [
            'deptrac' => [
                'ignore_uncovered_internal_classes' => false,
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(false, $this->container->getParameter('ignore_uncovered_internal_classes'));
    }

    public function testUseRelativePathFromDepfile(): void
    {
        $configs = [
            'deptrac' => [
                'use_relative_path_from_depfile' => false,
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertSame(false, $this->container->getParameter('use_relative_path_from_depfile'));
    }
}
