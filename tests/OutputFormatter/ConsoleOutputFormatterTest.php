<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap\AstInherit;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\Console\Command\AnalyzeCommand;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\OutputFormatter\ConsoleOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\Error;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Uncovered;
use Qossmic\Deptrac\RulesetEngine\Violation;
use Qossmic\Deptrac\RulesetEngine\Warning;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ConsoleOutputFormatterTest extends TestCase
{
    public function testGetName(): void
    {
        self::assertEquals('console', (new ConsoleOutputFormatter())->getName());
    }

    public function basicDataProvider(): iterable
    {
        $originalA = ClassLikeName::fromFQCN('OriginalA');
        $originalB = ClassLikeName::fromFQCN('OriginalB');

        yield [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeName::fromFQCN('ClassA'),
                        ClassLikeName::fromFQCN('ClassB'),
                        new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                        AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritA'), FileOccurrence::fromFilepath('originalA.php', 3))
                            ->withPath([
                                AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritB'), FileOccurrence::fromFilepath('originalA.php', 4)),
                                AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritC'), FileOccurrence::fromFilepath('originalA.php', 5)),
                                AstInherit::newExtends(ClassLikeName::fromFQCN('ClassInheritD'), FileOccurrence::fromFilepath('originalA.php', 6)),
                            ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [],
            'warnings' => [],
            '
                ClassA must not depend on ClassB (LayerA on LayerB)
                originalA.php::12
                ClassInheritD::6 ->
                ClassInheritC::5 ->
                ClassInheritB::4 ->
                ClassInheritA::3 ->
                OriginalB::12

                Report:
                Violations: 1
                Skipped violations: 0
                Uncovered: 0
                Allowed: 0
                Warnings:0
                Errors:0
            ',
        ];

        yield [
            [
                new Violation(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [],
            'warnings' => [],
            '
                OriginalA must not depend on OriginalB (LayerA on LayerB)
                originalA.php::12

                Report:
                Violations: 1
                Skipped violations: 0
                Uncovered: 0
                Allowed: 0
                Warnings:0
                Errors:0
            ',
        ];

        yield [
            [],
            [],
            'warnings' => [],
            '

                Report:
                Violations: 0
                Skipped violations: 0
                Uncovered: 0
                Allowed: 0
                Warnings:0
                Errors:0
            ',
        ];

        yield [
            [
                new SkippedViolation(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA',
                    'LayerB'
                ),
            ],
            [],
            'warnings' => [],
            '[SKIPPED] OriginalA must not depend on OriginalB (LayerA on LayerB)
            originalA.php::12
            
            Report:
            Violations: 0
            Skipped violations: 1
            Uncovered: 0
            Allowed: 0
            Warnings:0
            Errors:0
            ',
        ];

        yield [
            [
                new Uncovered(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA'
                ),
            ],
            [],
            'warnings' => [],
            '
                Uncovered dependencies:
                OriginalA has uncovered dependency on OriginalB (LayerA)
                originalA.php::12
                Report:
                Violations: 0
                Skipped violations: 0
                Uncovered: 1
                Allowed: 0
                Warnings:0
                Errors:0
            ',
        ];

        yield 'an error occurred' => [
            [],
            [new Error('an error occurred')],
            'warnings' => [],
            '[ERROR]anerroroccurredReport:Violations:0Skippedviolations:0Uncovered:0Allowed:0Warnings:0Errors:1',
        ];

        yield 'an warning occurred' => [
            [],
            [],
            'warnings' => [Warning::tokenLikeIsInMoreThanOneLayer(ClassLikeName::fromFQCN('Foo\Bar'), ['Layer 1', 'Layer 2'])],
            '[WARNING]Foo\Barisinmorethanonelayer["Layer1","Layer2"].Itisrecommendedthatonetokenshouldonlybeinonelayer.Report:Violations:0Skippedviolations:0Uncovered:0Allowed:0Warnings:1Errors:0',
        ];
    }

    /**
     * @dataProvider basicDataProvider
     */
    public function testBasic(array $rules, array $errors, array $warnings, string $expectedOutput): void
    {
        $bufferedOutput = new BufferedOutput();
        $output = new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );

        $formatter = new ConsoleOutputFormatter();
        $formatter->finish(
            new Context($rules, $errors, $warnings),
            $output,
            new OutputFormatterInput([
                AnalyzeCommand::OPTION_REPORT_UNCOVERED => true,
                AnalyzeCommand::OPTION_REPORT_SKIPPED => true,
            ])
        );

        $o = $bufferedOutput->fetch();
        self::assertEquals(
            $this->normalize($expectedOutput),
            $this->normalize($o)
        );
    }

    public function testWithoutSkippedViolations(): void
    {
        $originalA = ClassLikeName::fromFQCN('OriginalA');
        $originalB = ClassLikeName::fromFQCN('OriginalB');
        $rules = [
            new SkippedViolation(
                new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                'LayerA',
                'LayerB'
            ),
        ];

        $bufferedOutput = new BufferedOutput();
        $output = new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );

        $formatter = new ConsoleOutputFormatter();
        $formatter->finish(
            new Context($rules, [], []),
            $output,
            new OutputFormatterInput([
                AnalyzeCommand::OPTION_REPORT_UNCOVERED => true,
                AnalyzeCommand::OPTION_REPORT_SKIPPED => false,
            ])
        );

        $o = $bufferedOutput->fetch();

        $expectedOutput = '
            
            Report:
            Violations: 0
            Skipped violations: 1
            Uncovered: 0
            Allowed: 0
            Warnings:0
            Errors:0
            ';

        self::assertEquals(
            $this->normalize($expectedOutput),
            $this->normalize($o)
        );
    }

    public function testGetOptions(): void
    {
        self::assertCount(0, (new ConsoleOutputFormatter())->configureOptions());
    }

    private function normalize($str)
    {
        return str_replace(["\r", "\t", "\n", ' '], '', $str);
    }

    public function testConsoleOutputFormatterIsDisabledByDefault(): void
    {
        self::assertFalse((new ConsoleOutputFormatter())->enabledByDefault());
    }
}
