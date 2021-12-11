<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\RulesetEngine\Context;

interface OutputFormatterInterface
{
    /**
     * @return string used as an identifier to access to the formatter or to display something more user-friendly to the
     *                user when referring to the formatter
     *
     * @example "graphviz"
     */
    public static function getName(): string;

    /**
     * Name of the section in configuration file.
     */
    public static function getConfigName(): string;

    /**
     * Renders the final result.
     */
    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void;
}
