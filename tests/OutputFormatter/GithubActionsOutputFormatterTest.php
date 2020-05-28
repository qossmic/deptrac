<?php

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\OutputFormatter\GithubActionsOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Output\BufferedOutput;

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
        $output = new BufferedOutput();

        $formatter = new GithubActionsOutputFormatter();
        $formatter->finish(
            new Context($rules),
            $output,
            new OutputFormatterInput([])
        );

        $o = $output->fetch();
        static::assertEquals(
            $expectedOutput,
            $o
        );
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
    }
}
