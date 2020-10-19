<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\Console\Output;
use SensioLabs\Deptrac\RulesetEngine\Context;

interface OutputFormatterInterface
{
    /**
     * @return string used as an identifier to access to the formatter or to display something more user-friendly to the
     *                user when referring to the formatter
     *
     * @example "graphviz"
     */
    public function getName(): string;

    /**
     * @return OutputFormatterOption[]
     */
    public function configureOptions(): array;

    public function enabledByDefault(): bool;

    /**
     * Renders the final result.
     */
    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void;
}
