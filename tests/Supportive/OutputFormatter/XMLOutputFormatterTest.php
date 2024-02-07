<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Analyser\AnalysisResult;
use Qossmic\Deptrac\Contract\Ast\DependencyContext;
use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\Result\OutputResult;
use Qossmic\Deptrac\Contract\Result\SkippedViolation;
use Qossmic\Deptrac\Contract\Result\Violation;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInheritType;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\InheritDependency;
use Qossmic\Deptrac\Supportive\Console\Symfony\Style;
use Qossmic\Deptrac\Supportive\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Supportive\OutputFormatter\XMLOutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tests\Qossmic\Deptrac\Supportive\OutputFormatter\data\DummyViolationCreatingRule;

final class XMLOutputFormatterTest extends TestCase
{
    private static $actual_xml_report_file = 'actual-deptrac-report.xml';

    public function tearDown(): void
    {
        if (file_exists(__DIR__.'/data/'.self::$actual_xml_report_file)) {
            unlink(__DIR__.'/data/'.self::$actual_xml_report_file);
        }
    }

    public function testGetName(): void
    {
        self::assertSame('xml', (new XMLOutputFormatter())->getName());
    }

    public static function basicDataProvider(): iterable
    {
        yield [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency(ClassLikeToken::fromFQCN('OriginalA'), ClassLikeToken::fromFQCN('OriginalB'),
                            new DependencyContext(new FileOccurrence('ClassA.php', 12), DependencyType::PARAMETER)
                        ),
                        (new AstInherit(
                            ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('ClassA.php', 3),
                            AstInheritType::EXTENDS
                        ))->replacePath([
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritB'),
                                new FileOccurrence('ClassInheritA.php', 4),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritC'),
                                new FileOccurrence('ClassInheritB.php', 5),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritD'),
                                new FileOccurrence('ClassInheritC.php', 6),
                                AstInheritType::EXTENDS
                            ),
                        ])
                    ),
                    'LayerA',
                    'LayerB',
                    new DummyViolationCreatingRule()
                ),
            ],
            'expected-xml-report_1.xml',
        ];

        yield [
            [
                new Violation(
                    new Dependency(ClassLikeToken::fromFQCN('OriginalA'), ClassLikeToken::fromFQCN('OriginalB'),
                        new DependencyContext(new FileOccurrence('ClassA.php', 12), DependencyType::PARAMETER)
                    ),
                    'LayerA',
                    'LayerB',
                    new DummyViolationCreatingRule()
                ),
            ],
            'expected-xml-report_2.xml',
        ];

        yield [
            [],
            'expected-xml-report_3.xml',
        ];

        yield [
            [
                $violations = new SkippedViolation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency(ClassLikeToken::fromFQCN('OriginalA'), ClassLikeToken::fromFQCN('OriginalB'),
                            new DependencyContext(new FileOccurrence('ClassA.php', 12), DependencyType::PARAMETER)
                        ),
                        (new AstInherit(
                            ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('ClassA.php', 3),
                            AstInheritType::EXTENDS
                        ))->replacePath([
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritB'),
                                new FileOccurrence('ClassInheritA.php', 4),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritC'),
                                new FileOccurrence('ClassInheritB.php', 5),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritD'),
                                new FileOccurrence('ClassInheritC.php', 6),
                                AstInheritType::EXTENDS
                            ),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
                new SkippedViolation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassC'),
                        ClassLikeToken::fromFQCN('ClassD'),
                        new Dependency(ClassLikeToken::fromFQCN('OriginalA'), ClassLikeToken::fromFQCN('OriginalB'),
                            new DependencyContext(new FileOccurrence('ClassA.php', 12), DependencyType::PARAMETER)
                        ),
                        (new AstInherit(
                            ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('ClassA.php', 3),
                            AstInheritType::EXTENDS
                        ))->replacePath([
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritB'),
                                new FileOccurrence('ClassInheritA.php', 4),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritC'),
                                new FileOccurrence('ClassInheritB.php', 5),
                                AstInheritType::EXTENDS
                            ),
                            new AstInherit(
                                ClassLikeToken::fromFQCN('ClassInheritD'),
                                new FileOccurrence('ClassInheritC.php', 6),
                                AstInheritType::EXTENDS
                            ),
                        ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'expected-xml-report-with-skipped-violations.xml',
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testBasic(array $rules, $expectedOutputFile): void
    {
        $bufferedOutput = new BufferedOutput();

        $analysisResult = new AnalysisResult();
        foreach ($rules as $rule) {
            $analysisResult->addRule($rule);
        }

        $formatter = new XMLOutputFormatter();
        $formatter->finish(
            OutputResult::fromAnalysisResult($analysisResult),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput(__DIR__.'/data/'.self::$actual_xml_report_file, false, false, false)
        );

        self::assertXmlFileEqualsXmlFile(
            __DIR__.'/data/'.self::$actual_xml_report_file,
            __DIR__.'/data/'.$expectedOutputFile
        );
    }

    private function createSymfonyOutput(BufferedOutput $bufferedOutput): SymfonyOutput
    {
        return new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );
    }
}
