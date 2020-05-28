<?php

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassLikeName;
use SensioLabs\Deptrac\AstRunner\AstMap\FileOccurrence;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\OutputFormatter\GithubActionsOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\RulesetEngine\Context;
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
        $originalA = ClassLikeName::fromFQCN('OriginalA');
        $originalB = ClassLikeName::fromFQCN('OriginalB');

        yield 'No Rules, No Output' => [
            [],
            '',
        ];

        yield 'Simple Layer Violation' => [
            [
                new Violation(
                    new Dependency($originalA, $originalB, FileOccurrence::fromFilepath('originalA.php', 12)),
                    'LayerA',
                    'LayerB'
                ),
            ],
            "::error file=originalA.php,line=12::OriginalA must not depend on OriginalB (LayerA on LayerB)\n",
        ];
    }
}
