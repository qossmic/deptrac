<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\Dependency\InheritDependency;
use SensioLabs\Deptrac\OutputFormatter\JUnitOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\OutputFormatter\XMLOutputFormatter;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
use Symfony\Component\Console\Output\BufferedOutput;

class XMLOutputFormatterTest extends TestCase
{
    private static $actual_xml_report_file = 'actual-deptrac-report.xml';

    public function tearDown(): void
    {
        if (file_exists(__DIR__.'/data/'.static::$actual_xml_report_file)) {
            unlink(__DIR__.'/data/'.static::$actual_xml_report_file);
        }
    }

    public function testGetName(): void
    {
        static::assertSame('xml', (new XMLOutputFormatter())->getName());
    }

    public function basicDataProvider(): iterable
    {
        yield [
            [
                'LayerA',
                'LayerB',
            ],
            [
                new RulesetViolation(
                    new InheritDependency(
                        'ClassA',
                        'ClassB',
                        new Dependency('OriginalA', 12, 'OriginalB'),
                        AstInherit::newExtends('ClassInheritA', 3)->withPath([
                            AstInherit::newExtends('ClassInheritB', 4),
                            AstInherit::newExtends('ClassInheritC', 5),
                            AstInherit::newExtends('ClassInheritD', 6),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [],
            'expected-xml-report_1.xml',
        ];

        yield [
            [
                'LayerA',
                'LayerB',
            ],
            [
                new RulesetViolation(
                    new Dependency('OriginalA', 12, 'OriginalB'),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [],
            'expected-xml-report_2.xml',
        ];

        yield [
            [
            ],
            [
            ],
            [],
            'expected-xml-report_3.xml',
        ];

        yield [
            [
                'LayerA',
                'LayerB',
            ],
            [
                $violations = new RulesetViolation(
                    new InheritDependency(
                        'ClassA',
                        'ClassB',
                        new Dependency('OriginalA', 12, 'OriginalB'),
                        AstInherit::newExtends('ClassInheritA', 3)->withPath([
                            AstInherit::newExtends('ClassInheritB', 4),
                            AstInherit::newExtends('ClassInheritC', 5),
                            AstInherit::newExtends('ClassInheritD', 6),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
                new RulesetViolation(
                    new InheritDependency(
                        'ClassC',
                        'ClassD',
                        new Dependency('OriginalA', 12, 'OriginalB'),
                        AstInherit::newExtends('ClassInheritA', 3)->withPath([
                            AstInherit::newExtends('ClassInheritB', 4),
                            AstInherit::newExtends('ClassInheritC', 5),
                            AstInherit::newExtends('ClassInheritD', 6),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [
                $violations,
            ],
            'expected-xml-report-with-skipped-violations.xml',
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testBasic(array $layers, array $violations, array $skippedViolations, $expectedOutputFile): void
    {
        $classNameResolver = $this->prophesize(ClassNameLayerResolverInterface::class);
        $classNameResolver->getLayers()->willReturn($layers);

        $output = new BufferedOutput();

        $formatter = new XMLOutputFormatter();
        $formatter->finish(
            new DependencyContext(
                $this->prophesize(AstMap::class)->reveal(),
                $this->prophesize(Result::class)->reveal(),
                $classNameResolver->reveal(),
                $violations,
                $skippedViolations
            ),
            $output,
            new OutputFormatterInput(['dump-xml' => __DIR__.'/data/'.static::$actual_xml_report_file])
        );

        static::assertXmlFileEqualsXmlFile(
            __DIR__.'/data/'.static::$actual_xml_report_file,
            __DIR__.'/data/'.$expectedOutputFile
        );
    }

    public function testGetOptions(): void
    {
        static::assertCount(1, (new JUnitOutputFormatter())->configureOptions());
    }
}
