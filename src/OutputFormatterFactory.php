<?php


namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\OutputFormatter\OutputFormatterInterface;

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
