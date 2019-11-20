<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\OutputFormatter\GraphVizOutputFormatter;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\RulesetEngine\Allowed;
use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\Uncovered;
use SensioLabs\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Output\BufferedOutput;

class GraphVizOutputFormatterTest extends TestCase
{
    public function testGetName(): void
    {
        static::assertEquals('graphviz', (new GraphVizOutputFormatter())->getName());
    }

    public function testFinish(): void
    {
        $dotFile = __DIR__.'/data/graphviz.dot';

        $context = new Context([
            new Violation(new Dependency('ClassA', 0, 'ClassB'), 'LayerA', 'LayerB'),
            new Violation(new Dependency('ClassAB', 1, 'ClassBA'), 'LayerA', 'LayerB'),
            new Allowed(new Dependency('ClassA', 0, 'ClassC'), 'LayerA', 'LayerC'),
            new Uncovered(new Dependency('ClassA', 0, 'ClassD'), 'LayerC'),
        ]);

        $output = new BufferedOutput();
        $input = new OutputFormatterInput([
            'display' => false,
            'dump-image' => false,
            'dump-dot' => $dotFile,
            'dump-html' => false,
        ]);

        (new GraphVizOutputFormatter())->finish($context, $output, $input);

        static::assertSame(sprintf("Script dumped to %s\n", $dotFile), $output->fetch());
        static::assertFileEquals(__DIR__.'/data/graphviz-expected.dot', $dotFile);

        unlink($dotFile);
    }
}
