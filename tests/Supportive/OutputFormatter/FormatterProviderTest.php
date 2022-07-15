<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Supportive\OutputFormatter;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Supportive\DependencyInjection\Exception\InvalidServiceInLocatorException;
use Qossmic\Deptrac\Supportive\OutputFormatter\ConsoleOutputFormatter;
use Qossmic\Deptrac\Supportive\OutputFormatter\FormatterProvider;
use Qossmic\Deptrac\Supportive\OutputFormatter\TableOutputFormatter;
use stdClass;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class FormatterProviderTest extends TestCase
{
    public function testGet(): void
    {
        $formatterProvider = new FormatterProvider(new ServiceLocator([
            ConsoleOutputFormatter::getName() => static function () { return new ConsoleOutputFormatter(); },
            TableOutputFormatter::getName() => static function () { return new TableOutputFormatter(); },
        ]));

        self::assertTrue($formatterProvider->has(ConsoleOutputFormatter::getName()));
        self::assertInstanceOf(ConsoleOutputFormatter::class, $formatterProvider->get(ConsoleOutputFormatter::getName()));
        self::assertTrue($formatterProvider->has(TableOutputFormatter::getName()));
        self::assertInstanceOf(TableOutputFormatter::class, $formatterProvider->get(TableOutputFormatter::getName()));
        self::assertSame([
            ConsoleOutputFormatter::getName(),
            TableOutputFormatter::getName(),
        ], $formatterProvider->getKnownFormatters());
    }

    public function testContainerHasInvalidService(): void
    {
        $this->expectException(InvalidServiceInLocatorException::class);
        $this->expectExceptionMessage('Trying to get unsupported service "formatter1" from locator (expected "Qossmic\\Deptrac\\Contract\\OutputFormatter\\OutputFormatterInterface", but is "stdClass").');

        (new FormatterProvider(new ServiceLocator(['formatter1' => static fn () => new stdClass()])))->get('formatter1');
    }

    public function testContainerIsEmpty(): void
    {
        $this->expectException(ServiceNotFoundException::class);

        (new FormatterProvider(new ServiceLocator([])))->get('formatter1');
    }
}
