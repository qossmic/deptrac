<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Ast\AstMap\FileOccurrence;
use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Dependency\Dependency;
use Qossmic\Deptrac\Dependency\InheritDependency;
use Qossmic\Deptrac\OutputFormatter\ConsoleOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Result\Error;
use Qossmic\Deptrac\Result\LegacyResult;
use Qossmic\Deptrac\Result\SkippedViolation;
use Qossmic\Deptrac\Result\Uncovered;
use Qossmic\Deptrac\Result\Violation;
use Qossmic\Deptrac\Result\Warning;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ConsoleOutputFormatterTest extends TestCase
{
    public function testGetName(): void
    {
        self::assertSame('console', (new ConsoleOutputFormatter())->getName());
    }

    public function basicDataProvider(): iterable
    {
        $originalA = ClassLikeToken::fromFQCN('OriginalA');
        $originalB = ClassLikeToken::fromFQCN('OriginalB');

        yield [
            [
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                        AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritA'), FileOccurrence::fromFilepath('originalA.php', 3))
                            ->withPath([
                                AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritB'), FileOccurrence::fromFilepath('originalA.php', 4)),
                                AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritC'), FileOccurrence::fromFilepath('originalA.php', 5)),
                                AstInherit::newExtends(ClassLikeToken::fromFQCN('ClassInheritD'), FileOccurrence::fromFilepath('originalA.php', 6)),
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
            'warnings' => [Warning::tokenIsInMoreThanOneLayer(ClassLikeToken::fromFQCN('Foo\Bar'), ['Layer 1', 'Layer 2'])],
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
            new LegacyResult($rules, $errors, $warnings),
            $output,
            new OutputFormatterInput(
                null,
                true,
                true,
                false,
            )
        );

        $o = $bufferedOutput->fetch();
        self::assertSame(
            $this->normalize($expectedOutput),
            $this->normalize($o)
        );
    }

    public function testWithoutSkippedViolations(): void
    {
        $originalA = ClassLikeToken::fromFQCN('OriginalA');
        $originalB = ClassLikeToken::fromFQCN('OriginalB');
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
            new LegacyResult($rules, [], []),
            $output,
            new OutputFormatterInput(
                null,
                false,
                true,
                false,
            )
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

        self::assertSame(
            $this->normalize($expectedOutput),
            $this->normalize($o)
        );
    }

    private function normalize($str)
    {
        return str_replace(["\r", "\t", "\n", ' '], '', $str);
    }
}
