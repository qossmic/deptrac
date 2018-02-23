<?php

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class OutputFormatterFactory
{
    /**
     * @var OutputFormatterInterface[]
     */
    protected $formatters;

    /**
     * @param OutputFormatterInterface[] $formatters
     */
    public function __construct(array $formatters)
    {
        $this->formatters = $formatters;
    }

    /**
     * @return InputOption[]
     */
    public function getFormatterOptions(): array
    {
        $arguments = [];

        foreach ($this->formatters as $formatter) {
            $formatterArguments = $formatter->configureOptions();

            $arguments[] = new InputOption(
                'formatter-'.$formatter->getName(),
                null,
                InputOption::VALUE_OPTIONAL,
                'to disable the '.$formatter->getName().' fomatter, set this argument to 0',
                1
            );

            foreach ($formatterArguments as $formatterArgument) {
                $arguments[] = new InputOption(
                    'formatter-'.$formatter->getName().'-'.$formatterArgument->getName(),
                    null,
                    $formatterArgument->getMode(),
                    $formatterArgument->getDescription(),
                    $formatterArgument->getDefault()
                );
            }
        }

        return $arguments;
    }

    /**
     * @param OutputFormatterInterface $formatter
     * @param InputInterface           $input
     *
     * @return bool
     */
    private function isFormatterActive(OutputFormatterInterface $formatter, InputInterface $input): bool
    {
        return (bool) $input->getOption('formatter-'.$formatter->getName());
    }

    /**
     * @param InputInterface $input
     *
     * @return OutputFormatterInterface[]
     */
    public function getActiveFormatters(InputInterface $input): array
    {
        return array_values(array_filter($this->formatters, function (OutputFormatterInterface $formatter) use ($input) {
            return $this->isFormatterActive($formatter, $input);
        }));
    }

    public function getOutputFormatterInput(OutputFormatterInterface $outputFormatter, InputInterface $input): OutputFormatterInput
    {
        $buffer = [];
        foreach ($input->getOptions() as $k => $v) {
            if (0 !== strpos($k, 'formatter-'.$outputFormatter->getName().'-')) {
                continue;
            }

            $option = substr($k, strlen('formatter-'.$outputFormatter->getName().'-'));

            $buffer[$option] = $v;
        }

        return new OutputFormatterInput($buffer);
    }

    /**
     * @param string $name
     *
     * @throws \LogicException if formatter does not exists
     *
     * @return OutputFormatterInterface
     */
    public function getFormatterByName(string $name): OutputFormatterInterface
    {
        foreach ($this->formatters as $formatter) {
            if (strtolower($name) !== strtolower($formatter->getName())) {
                continue;
            }

            return $formatter;
        }

        throw new \LogicException(sprintf(
            'Formatter %s does not exists, did you mean %s?',
            $name,
            implode(
                ', ',
                array_map(function (OutputFormatterInterface $f) {
                    return $f->getName();
                }, $this->formatters)
            )
        ));
    }
}
