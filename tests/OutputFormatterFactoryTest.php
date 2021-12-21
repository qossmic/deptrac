<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac;

use LogicException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\OutputFormatter\ConsoleOutputFormatter;
use Qossmic\Deptrac\OutputFormatter\TableOutputFormatter;
use Qossmic\Deptrac\OutputFormatterFactory;

final class OutputFormatterFactoryTest extends TestCase
{
    public function testGetFormatterByName(): void
    {
        $formatterFactory = new OutputFormatterFactory([
            $formatter1 = new ConsoleOutputFormatter(),
            $formatter2 = new TableOutputFormatter(),
        ]);

        self::assertSame($formatter1, $formatterFactory->getFormatterByName(ConsoleOutputFormatter::getName()));
        self::assertSame($formatter2, $formatterFactory->getFormatterByName(TableOutputFormatter::getName()));
    }

    public function testGetFormatterByNameNotFound(): void
    {
        $this->expectException(LogicException::class);

        (new OutputFormatterFactory([]))->getFormatterByName('formatter1');
    }
}
