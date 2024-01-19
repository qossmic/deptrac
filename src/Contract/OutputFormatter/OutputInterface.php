<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Contract\OutputFormatter;

/**
 * Wrapper around Symfony Output.
 */
interface OutputInterface
{
    public function writeFormatted(string $message) : void;
    /**
     * @param string|string[] $message
     */
    public function writeLineFormatted(string|array $message) : void;
    public function writeRaw(string $message) : void;
    public function getStyle() : \Qossmic\Deptrac\Contract\OutputFormatter\OutputStyleInterface;
    public function isVerbose() : bool;
    public function isDebug() : bool;
}
