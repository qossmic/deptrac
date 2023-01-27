<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Analyser\AnalysisResultBuilder;
use Qossmic\Deptrac\Contract\Ast\DependencyType;
use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputFormatterInput;
use Qossmic\Deptrac\Contract\Result\Error;
use Qossmic\Deptrac\Contract\Result\OutputResult;
use Qossmic\Deptrac\Contract\Result\SkippedViolation;
use Qossmic\Deptrac\Contract\Result\Uncovered;
use Qossmic\Deptrac\Contract\Result\Violation;
use Qossmic\Deptrac\Contract\Result\Warning;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInherit;
use Qossmic\Deptrac\Core\Ast\AstMap\AstInheritType;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Dependency\Dependency;
use Qossmic\Deptrac\Core\Dependency\InheritDependency;
use Qossmic\Deptrac\Supportive\Console\Symfony\Style;
use Qossmic\Deptrac\Supportive\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Supportive\OutputFormatter\GithubActionsOutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

use const PHP_EOL;

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

        $resultBuilder = new AnalysisResultBuilder();
        foreach ($rules as $rule) {
            $resultBuilder->add($rule);
        }
        foreach ($errors as $error) {
            $resultBuilder->addError($error);
        }
        $resultBuilder->addWarnings($warnings);

        $formatter = new GithubActionsOutputFormatter();
        $formatter->finish(
            $resultBuilder->build(),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput(
                null,
                true,
                true,
                false
            )
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

        $originalA = ClassLikeToken::fromFQCN('\ACME\OriginalA');
        $originalB = ClassLikeToken::fromFQCN('\ACME\OriginalB');
        $originalAOccurrence = new FileOccurrence('/home/testuser/originalA.php', 12);

        yield 'Simple Violation' => [
            'violations' => [
                new Violation(
                    new Dependency($originalA, $originalB, $originalAOccurrence, DependencyType::PARAMETER),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'errors' => [],
            'warnings' => [],
            "::error file=/home/testuser/originalA.php,line=12::ACME\OriginalA must not depend on ACME\OriginalB (LayerA on LayerB)".PHP_EOL,
        ];

        yield 'Skipped Violation' => [
            'violations' => [
                new SkippedViolation(
                    new Dependency($originalA, $originalB, $originalAOccurrence, DependencyType::PARAMETER),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'errors' => [],
            'warnings' => [],
            "::warning file=/home/testuser/originalA.php,line=12::[SKIPPED] ACME\OriginalA must not depend on ACME\OriginalB (LayerA on LayerB)".PHP_EOL,
        ];

        yield 'Uncovered Dependency' => [
            'violations' => [
                new Uncovered(
                    new Dependency($originalA, $originalB, $originalAOccurrence, DependencyType::PARAMETER),
                    'LayerA'
                ),
            ],
            'errors' => [],
            'warnings' => [],
            "::warning file=/home/testuser/originalA.php,line=12::ACME\OriginalA has uncovered dependency on ACME\OriginalB (LayerA)".PHP_EOL,
        ];

        yield 'Inherit dependency' => [
            'violations' => [
                new Violation(
                    new InheritDependency(
                        ClassLikeToken::fromFQCN('ClassA'),
                        ClassLikeToken::fromFQCN('ClassB'),
                        new Dependency($originalA, $originalB, new FileOccurrence('originalA.php', 12), DependencyType::PARAMETER),
                        (new AstInherit(
                            ClassLikeToken::fromFQCN('ClassInheritA'), new FileOccurrence('originalA.php', 3),
                            AstInheritType::EXTENDS
                        ))
                            ->withPath([
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritB'),
                                    new FileOccurrence('originalA.php', 4),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritC'),
                                    new FileOccurrence('originalA.php', 5),
                                    AstInheritType::EXTENDS
                                ),
                                new AstInherit(
                                    ClassLikeToken::fromFQCN('ClassInheritD'),
                                    new FileOccurrence('originalA.php', 6),
                                    AstInheritType::EXTENDS
                                ),
                            ])
                    ),
                    'LayerA',
                    'LayerB'
                ),
            ],
            'errors' => [],
            'warnings' => [],
            "::error file=originalA.php,line=12::ClassA must not depend on ClassB (LayerA on LayerB)%0AClassInheritD::6 ->%0AClassInheritC::5 ->%0AClassInheritB::4 ->%0AClassInheritA::3 ->%0AACME\OriginalB::12".PHP_EOL,
        ];

        yield 'an error occurred' => [
            'violations' => [],
            'errors' => [new Error('an error occurred')],
            'warnings' => [],
            '::error ::an error occurred'.PHP_EOL,
        ];

        yield 'an warning occurred' => [
            'violations' => [],
            'errors' => [],
            'warnings' => [
                Warning::tokenIsInMoreThanOneLayer(ClassLikeToken::fromFQCN('Foo\Bar')->toString(), ['Layer 1', 'Layer 2']),
            ],
            "::warning ::Foo\Bar is in more than one layer [\"Layer 1\", \"Layer 2\"]. It is recommended that one token should only be in one layer.".PHP_EOL,
        ];
    }

    public function testWithoutSkippedViolations(): void
    {
        $originalA = ClassLikeToken::fromFQCN('\ACME\OriginalA');
        $originalB = ClassLikeToken::fromFQCN('\ACME\OriginalB');
        $originalAOccurrence = new FileOccurrence('/home/testuser/originalA.php', 12);

        $rules = [
            new SkippedViolation(
                new Dependency($originalA, $originalB, $originalAOccurrence, DependencyType::PARAMETER),
                'LayerA',
                'LayerB'
            ),
        ];

        $bufferedOutput = new BufferedOutput();

        $formatter = new GithubActionsOutputFormatter();
        $formatter->finish(
            new OutputResult($rules, [], []),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput(
                null,
                false,
                true,
                false,
            )
        );

        self::assertSame('', $bufferedOutput->fetch());
    }

    public function testUncoveredWithFailOnUncoveredAreReportedAsError(): void
    {
        $originalA = ClassLikeToken::fromFQCN('\ACME\OriginalA');
        $originalB = ClassLikeToken::fromFQCN('\ACME\OriginalB');
        $originalAOccurrence = new FileOccurrence('/home/testuser/originalA.php', 12);

        $analysisResult = new AnalysisResultBuilder();
        $analysisResult->add(
            new Uncovered(
                new Dependency($originalA, $originalB, $originalAOccurrence, DependencyType::PARAMETER),
                'LayerA'
            )
        );

        $bufferedOutput = new BufferedOutput();

        $formatter = new GithubActionsOutputFormatter();
        $formatter->finish(
            $analysisResult->build(),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput(
                null,
                false,
                true,
                true,
            )
        );

        self::assertSame(
            "::error file=/home/testuser/originalA.php,line=12::ACME\OriginalA has uncovered dependency on ACME\OriginalB (LayerA)".PHP_EOL,
            $bufferedOutput->fetch()
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
