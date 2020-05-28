<?php

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\OutputFormatter\GithubActionsOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\RulesetEngine\Context;
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
    }
}
