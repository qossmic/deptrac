<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console;

use Symfony\Component\Console\Helper\TableSeparator;

interface OutputStyle
{
    public function title(string $message): void;

    public function section(string $message): void;

    /**
     * @param string|string[] $message
     */
    public function success($message): void;

    /**
     * @param string|string[] $message
     */
    public function error($message): void;

    /**
     * @param string|string[] $message
     */
    public function warning($message): void;

    /**
     * @param string|string[] $message
     */
    public function note($message): void;

    /**
     * @param string|string[] $message
     */
    public function caution($message): void;

    /**
     * @param string|array|TableSeparator ...$list
     */
    public function definitionList(...$list): void;

    /**
     * @param mixed[] $headers
     * @param mixed[] $rows
     */
    public function table(array $headers, array $rows): void;

    public function newLine(int $count = 1): void;

    public function progressStart(int $max = 0): void;

    public function progressAdvance(int $step = 1): void;

    public function progressFinish(): void;
}
