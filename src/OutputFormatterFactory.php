<?php

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInput;
use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class OutputFormatterFactory
{
    /** @var OutputFormatterInterface[] */
    protected $formatters;

    /** @param OutputFormatterInterface[] $formatters */
    public function __construct(array $formatters)
    {
        $this->formatters = $formatters;
    }

    /**
     * @return InputOption[]
     */
    public function getFormatterOptions()
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
    private function isFormatterActive(OutputFormatterInterface $formatter, InputInterface $input)
    {
        return (bool) $input->getOption('formatter-'.$formatter->getName());
    }

    /**
     * @param InputInterface $input
     *
     * @return OutputFormatterInterface[]
     */
    public function getActiveFormatters(InputInterface $input)
    {
        return array_values(array_filter($this->formatters, function (OutputFormatterInterface $formatter) use ($input) {
            return $this->isFormatterActive($formatter, $input);
        }));
    }

    /**
     * @param OutputFormatterInterface $outputFormatter
     * @param InputInterface           $input
     *
     * @return OutputFormatterInput
     */
    public function getOutputFormatterInput(OutputFormatterInterface $outputFormatter, InputInterface $input)
    {
        $buffer = [];
        foreach ($input->getOptions() as $k => $v) {
            if (strpos($k, 'formatter-'.$outputFormatter->getName().'-') !== 0) {
                continue;
            }

            $option = substr($k, strlen('formatter-'.$outputFormatter->getName().'-'));

            $buffer[$option] = $v;
        }

        return new OutputFormatterInput($buffer);
    }

    /**
     * @param $name
     *
     * @return OutputFormatterInterface
     */
    public function getFormatterByName($name)
    {
        foreach ($this->formatters as $formatter) {
            if (strtolower($name) != strtolower($formatter->getName())) {
                continue;
            }

            return $formatter;
        }

        throw new \LogicException(sprintf(
            'Formatter %s does not exists, did you mean %s?',
            $name,
            implode(', ',
                array_map(function (OutputFormatterInterface $f) { return $f->getName(); }, $this->formatters)
            )
        ));
    }
}
