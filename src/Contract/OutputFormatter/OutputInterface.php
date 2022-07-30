<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\OutputFormatter;

interface OutputInterface
{
    public function writeFormatted(string $message): void;

    /**
     * @param string|string[] $message
     */
    public function writeLineFormatted($message): void;

    public function writeRaw(string $message): void;

    public function getStyle(): OutputStyleInterface;

    public function isVerbose(): bool;

    public function isDebug(): bool;
}
