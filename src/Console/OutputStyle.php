<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Console;

use Symfony\Component\Console\Helper\TableSeparator;

interface OutputStyle
{
    public function title(string $message): void;

    public function section(string $message): void;

    public function success(string $message): void;

    public function error(string $message): void;

    public function warning(string $message): void;

    public function note(string $message): void;

    public function caution(string $message): void;

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
