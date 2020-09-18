<?php

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Console\Symfony\Style;
use SensioLabs\Deptrac\Console\Symfony\SymfonyOutput;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\OutputFormatter\GithubActionsOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Uncovered;
use SensioLabs\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tests\SensioLabs\Deptrac\EmptyEnv;

class GithubActionsOutputFormatterTest extends TestCase
{
    public function testGetName()
    {
        static::assertEquals('github-actions', (new GithubActionsOutputFormatter())->getName());
    }

    /**
     * @dataProvider finishProvider
     */
    public function testFinish(array $rules, string $expectedOutput): void
    {
        $bufferedOutput = new BufferedOutput();

        $formatter = new GithubActionsOutputFormatter();
        $formatter->finish(
            new Context($rules),
            $this->createSymfonyOutput($bufferedOutput),
            new OutputFormatterInput(['report-uncovered' => true])
        );

        static::assertEquals($expectedOutput, $bufferedOutput->fetch());
    }

    public function finishProvider()
    {
        yield 'No Rules, No Output' => [
            [],
            '',
        ];

        $originalA = ClassLikeName::fromFQCN('\ACME\OriginalA');
        $originalB = ClassLikeName::fromFQCN('\ACME\OriginalB');
        $originalAOccurrence = FileOccurrence::fromFilepath('/home/testuser/originalA.php', 12);

        yield 'Simple Violation' => [
            [
                new Violation(
                    new Dependency($originalA, $originalB, $originalAOccurrence),
                    'LayerA',
                    'LayerB'
                ),
            ],
            "::error file=/home/testuser/originalA.php,line=12::ACME\OriginalA must not depend on ACME\OriginalB (LayerA on LayerB)\n",
        ];

        yield 'Skipped Violation' => [
            [
                new SkippedViolation(
                    new Dependency($originalA, $originalB, $originalAOccurrence),
                    'LayerA',
                    'LayerB'
                ),
            ],
            "::warning file=/home/testuser/originalA.php,line=12::[SKIPPED] ACME\OriginalA must not depend on ACME\OriginalB (LayerA on LayerB)\n",
        ];

        yield 'Uncovered Dependency' => [
            [
                new Uncovered(
                    new Dependency($originalA, $originalB, $originalAOccurrence),
                    'LayerA'
                ),
            ],
            "::warning file=/home/testuser/originalA.php,line=12::ACME\OriginalA has uncovered dependency on ACME\OriginalB (LayerA)\n",
        ];
    }

    public function testGithubActionsOutputFormatterIsNotEnabledByDefault(): void
    {
        static::assertFalse((new GithubActionsOutputFormatter(new EmptyEnv()))->enabledByDefault());
    }

    public function testGetOptions(): void
    {
        static::assertCount(1, (new GithubActionsOutputFormatter(new EmptyEnv()))->configureOptions());
    }

    private function createSymfonyOutput(BufferedOutput $bufferedOutput): SymfonyOutput
    {
        return new SymfonyOutput(
            $bufferedOutput,
            new Style(new SymfonyStyle($this->createMock(InputInterface::class), $bufferedOutput))
        );
    }
}
