<?php

declare(strict_types=1);

namespace Tests\SensioLabs\Deptrac\OutputFormatter;

use PHPUnit\Framework\TestCase;
use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\Dependency\Dependency;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\OutputFormatter\SarbOutputFormatter;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
use Symfony\Component\Console\Output\BufferedOutput;

class SarbOutputFormatterTest extends TestCase
{
    private static $actual_junit_report_file = 'actual-sarb.json';

    public function tearDown(): void
    {
        if (file_exists(__DIR__ . '/data/' . static::$actual_junit_report_file)) {
            unlink(__DIR__ . '/data/' . static::$actual_junit_report_file);
        }
    }

    public function testGetName(): void
    {
        static::assertSame('sarb', (new SarbOutputFormatter())->getName());
    }

    public function testGenerateJson(): void
    {
        $violations = [
            new RulesetViolation(
                new Dependency(
                    '/home/project/Person.php',
                    'Acme\\Entity\\Person',
                    12,
                    'Acme\\Controller\\User'),
                'Layer A',
                'Layer B'),
            new RulesetViolation(
                new Dependency(
                    '/home/project/User.php',
                    'Acme\\Entity\\User',
                    8,
                    'Acme\\Controller\\Course'),
                'Layer A',
                'Layer B'),
        ];


        $output = new BufferedOutput();

        $formatter = new SarbOutputFormatter();
        $formatter->finish(
            new DependencyContext(
                $this->prophesize(AstMap::class)->reveal(),
                $this->prophesize(Result::class)->reveal(),
                $this->prophesize(ClassNameLayerResolverInterface::class)->reveal(),
                $violations,
                []
            ),
            $output,
            new OutputFormatterInput(['dump-sarb' => __DIR__ . '/data/' . static::$actual_junit_report_file])
        );

        static::assertJsonFileEqualsJsonFile(
            __DIR__ . '/data/' . static::$actual_junit_report_file,
            __DIR__ . '/data/expected-sarb.json'
        );
    }

    public function testGetOptions(): void
    {
        static::assertCount(1, (new SarbOutputFormatter())->configureOptions());
    }
}
