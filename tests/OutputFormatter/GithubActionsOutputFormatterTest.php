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
use Qossmic\Deptrac\OutputFormatter\GithubActionsOutputFormatter;
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
use Tests\Qossmic\Deptrac\EmptyEnv;

final class GithubActionsOutputFormatterTest extends TestCase
{
    public function testGetName(): void
    {
        self::assertSame('github-actions', (new GithubActionsOutputFormatter())->getName());
    }

    /**
     * @dataProvider finishProvider
     */
    public function testFinish(array $rules, array $errors, array $warnings, string $expectedOutput): void
    {
        $bufferedOutput = new BufferedOutput();

        $formatter = new GithubActionsOutputFormatter();
        $formatter->finish(
            new Context($rules, $errors, $warnings),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput([
                AnalyzeCommand::OPTION_REPORT_SKIPPED => true,
                AnalyzeCommand::OPTION_REPORT_UNCOVERED => true,
                AnalyzeCommand::OPTION_FAIL_ON_UNCOVERED => false,
            ])
        );

        self::assertSame($expectedOutput, $bufferedOutput->fetch());
    }

    public function finishProvider(): iterable
    {
        yield 'No Rules, No Output' => [
            'rules' => [],
            'errors' => [],
            'warnings' => [],
            '',
        ];

        $originalA = ClassLikeName::fromFQCN('\ACME\OriginalA');
        $originalB = ClassLikeName::fromFQCN('\ACME\OriginalB');
        $originalAOccurrence = FileOccurrence::fromFilepath('/home/testuser/originalA.php', 12);

        yield 'Simple Violation' => [
            'violations' => [
                new Violation(
                    new Dependency($originalA, $originalB, $originalAOccurrence),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'errors' => [],
            'warnings' => [],
            "::error file=/home/testuser/originalA.php,line=12::ACME\OriginalA must not depend on ACME\OriginalB (LayerA on LayerB)\n",
        ];

        yield 'Skipped Violation' => [
            'violations' => [
                new SkippedViolation(
                    new Dependency($originalA, $originalB, $originalAOccurrence),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'errors' => [],
            'warnings' => [],
            "::warning file=/home/testuser/originalA.php,line=12::[SKIPPED] ACME\OriginalA must not depend on ACME\OriginalB (LayerA on LayerB)\n",
        ];

        yield 'Uncovered Dependency' => [
            'violations' => [
                new Uncovered(
                    new Dependency($originalA, $originalB, $originalAOccurrence),
                    'LayerA'
                ),
            ],
            'errors' => [],
            'warnings' => [],
            "::warning file=/home/testuser/originalA.php,line=12::ACME\OriginalA has uncovered dependency on ACME\OriginalB (LayerA)\n",
        ];

        yield 'Inherit dependency' => [
            'violations' => [
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
            'errors' => [],
            'warnings' => [],
            "::error file=originalA.php,line=12::ClassA must not depend on ClassB (LayerA on LayerB)%0AClassInheritD::6 ->%0AClassInheritC::5 ->%0AClassInheritB::4 ->%0AClassInheritA::3 ->%0AACME\OriginalB::12\n",
        ];

        yield 'an error occurred' => [
            'violations' => [],
            'errors' => [new Error('an error occurred')],
            'warnings' => [],
            "::error ::an error occurred\n",
        ];

        yield 'an warning occurred' => [
            'violations' => [],
            'errors' => [],
            'warnings' => [
                Warning::tokenLikeIsInMoreThanOneLayer(ClassLikeName::fromFQCN('Foo\Bar'), ['Layer 1', 'Layer 2']),
            ],
            "::warning ::Foo\Bar is in more than one layer [\"Layer 1\", \"Layer 2\"]. It is recommended that one token should only be in one layer.\n",
        ];
    }

    public function testWithoutSkippedViolations(): void
    {
        $originalA = ClassLikeName::fromFQCN('\ACME\OriginalA');
        $originalB = ClassLikeName::fromFQCN('\ACME\OriginalB');
        $originalAOccurrence = FileOccurrence::fromFilepath('/home/testuser/originalA.php', 12);

        $rules = [
            new SkippedViolation(
                new Dependency($originalA, $originalB, $originalAOccurrence),
                'LayerA',
                'LayerB'
            ),
        ];

        $bufferedOutput = new BufferedOutput();

        $formatter = new GithubActionsOutputFormatter();
        $formatter->finish(
            new Context($rules, [], []),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput([
                AnalyzeCommand::OPTION_REPORT_SKIPPED => false,
                AnalyzeCommand::OPTION_REPORT_UNCOVERED => true,
                AnalyzeCommand::OPTION_FAIL_ON_UNCOVERED => false,
            ])
        );

        self::assertSame('', $bufferedOutput->fetch());
    }

    public function testUncoveredWithFailOnUncoveredAreReportedAsError(): void
    {
        $originalA = ClassLikeName::fromFQCN('\ACME\OriginalA');
        $originalB = ClassLikeName::fromFQCN('\ACME\OriginalB');
        $originalAOccurrence = FileOccurrence::fromFilepath('/home/testuser/originalA.php', 12);

        $rules = [
            new Uncovered(
                new Dependency($originalA, $originalB, $originalAOccurrence),
                'LayerA'
            ),
        ];

        $bufferedOutput = new BufferedOutput();

        $formatter = new GithubActionsOutputFormatter();
        $formatter->finish(
            new Context($rules, [], []),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput([
                AnalyzeCommand::OPTION_REPORT_SKIPPED => false,
                AnalyzeCommand::OPTION_REPORT_UNCOVERED => true,
                AnalyzeCommand::OPTION_FAIL_ON_UNCOVERED => true,
            ])
        );

        self::assertSame(
            "::error file=/home/testuser/originalA.php,line=12::ACME\OriginalA has uncovered dependency on ACME\OriginalB (LayerA)\n",
            $bufferedOutput->fetch()
        );
    }

    public function testGithubActionsOutputFormatterIsNotEnabledByDefault(): void
    {
        self::assertFalse((new GithubActionsOutputFormatter(new EmptyEnv()))->enabledByDefault());
    }

    public function testGetOptions(): void
    {
        self::assertCount(0, (new GithubActionsOutputFormatter(new EmptyEnv()))->configureOptions());
    }

    private function createSymfonyOutput(BufferedOutput $bufferedOutput): SymfonyOutput
    {
        return new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );
    }
}
