<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use LogicException;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterInterface;

class OutputFormatterFactory
{
    /**
     * @var array<string, OutputFormatterInterface>
     */
    protected array $formatters = [];

    /**
     * @param iterable<OutputFormatterInterface> $formatters
     */
    public function __construct(iterable $formatters)
    {
        foreach ($formatters as $formatter) {
            $this->formatters[$formatter->getName()] = $formatter;
        }
    }

    /**
     * @throws LogicException if formatter does not exist
     */
    public function getFormatterByName(string $name): OutputFormatterInterface
    {
        foreach ($this->formatters as $formatter) {
            if (strtolower($name) !== strtolower($formatter::getName())) {
                continue;
            }

            return $formatter;
        }

        throw new LogicException(sprintf('Formatter %s does not exists, did you mean %s?', $name, implode(', ', array_map(static function (OutputFormatterInterface $f): string { return $f->getName(); }, $this->formatters))));
    }

    /**
     * @return string[]
     */
    public function getFormatterNames(): array
    {
        return array_keys($this->formatters);
    }
}
