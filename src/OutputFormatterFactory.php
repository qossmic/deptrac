<?php

declare(strict_types=1);

namespace Qossmic\Deptrac;

use Qossmic\Deptrac\OutputFormatter\OutputFormatterInterface;
use Qossmic\Deptrac\OutputFormatter\OutputFormatterOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class OutputFormatterFactory
{
    /**
     * @var array<string, OutputFormatterInterface>
     */
    protected $formatters = [];

    /**
     * @param OutputFormatterInterface[] $formatters
     */
    public function __construct($formatters)
    {
        foreach ($formatters as $formatter) {
            $this->addFormatter($formatter);
        }
    }

    /**
     * @return InputOption[]
     */
    public function getFormatterOptions(): array
    {
        $arguments = [];

        foreach ($this->formatters as $formatter) {
            $arguments[] = $this->createFormatterOption($formatter);

            $formatterArguments = $formatter->configureOptions();

            foreach ($formatterArguments as $formatterArgument) {
                $arguments[] = $this->createFormatterArgumentOption($formatter, $formatterArgument);
            }
        }

        return $arguments;
    }

    /**
     * @return OutputFormatterInterface[]
     */
    public function getActiveFormatters(InputInterface $input): array
    {
        return array_values(array_filter($this->formatters, function (OutputFormatterInterface $formatter) use ($input): bool {
            return $this->isFormatterActive($formatter, $input);
        }));
    }

    /**
     * @throws \LogicException if formatter does not exists
     */
    public function getFormatterByName(string $name): OutputFormatterInterface
    {
        foreach ($this->formatters as $formatter) {
            if (strtolower($name) !== strtolower($formatter->getName())) {
                continue;
            }

            return $formatter;
        }

        throw new \LogicException(sprintf('Formatter %s does not exists, did you mean %s?', $name, implode(', ', array_map(static function (OutputFormatterInterface $f): string { return $f->getName(); }, $this->formatters))));
    }

    private function isFormatterActive(OutputFormatterInterface $formatter, InputInterface $input): bool
    {
        $option = $input->getOption('formatter-'.$formatter->getName());

        return true === filter_var($option, FILTER_VALIDATE_BOOLEAN);
    }

    private function addFormatter(OutputFormatterInterface $formatter): void
    {
        $this->formatters[$formatter->getName()] = $formatter;
    }

    private function createFormatterOption(OutputFormatterInterface $formatter): InputOption
    {
        $description = $formatter->enabledByDefault()
            ? 'to disable the '.$formatter->getName().' formatter, set this argument to "false"'
            : 'to activate the '.$formatter->getName().' formatter, set this argument to "true"';

        return new InputOption(
            'formatter-'.$formatter->getName(),
            null,
            InputOption::VALUE_OPTIONAL,
            '<fg=yellow>[DEPRECATED]</> '.$description
        );
    }

    private function createFormatterArgumentOption(OutputFormatterInterface $formatter, OutputFormatterOption $formatterArgument): InputOption
    {
        return new InputOption(
            $formatterArgument->getName(),
            null,
            $formatterArgument->getMode(),
            $formatterArgument->getDescription(),
            $formatterArgument->getDefault()
        );
    }

    /**
     * @param string[] $names
     *
     * @return OutputFormatterInterface[]
     */
    public function getFormattersByNames(array $names): array
    {
        $invalidNames = [];
        foreach ($names as $name) {
            if (!isset($this->formatters[$name])) {
                $invalidNames[] = $name;
            }
        }

        if ([] !== $invalidNames) {
            throw new \InvalidArgumentException(sprintf('Following formatters ["%s"] are not supported.', implode('", "', $invalidNames)));
        }

        return array_values(
            array_filter(
                $this->formatters,
                static function (string $key) use ($names) {
                    return in_array($key, $names, true);
                },
                ARRAY_FILTER_USE_KEY
            )
        );
    }

    /**
     * @return string[]
     */
    public function getFormatterNames(): array
    {
        return array_keys($this->formatters);
    }

    /**
     * @return OutputFormatterInterface[]
     */
    public function getFormattersEnabledByDefault(): array
    {
        return array_values(array_filter($this->formatters, static function (OutputFormatterInterface $formatter) {
            return $formatter->enabledByDefault();
        }));
    }
}
