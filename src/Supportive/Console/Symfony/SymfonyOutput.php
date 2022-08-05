<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Symfony;

use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputStyleInterface;
use Symfony\Component\Console\Output\OutputInterface as SymfonyOutputInterface;

/**
 * @internal
 */
class SymfonyOutput implements OutputInterface
{
    private SymfonyOutputInterface $symfonyOutput;
    private OutputStyleInterface $style;

    public function __construct(
        SymfonyOutputInterface $symfonyOutput,
        OutputStyleInterface $style
    ) {
        $this->symfonyOutput = $symfonyOutput;
        $this->style = $style;
    }

    public function writeFormatted(string $message): void
    {
        $this->symfonyOutput->write($message, false, SymfonyOutputInterface::OUTPUT_NORMAL);
    }

    /**
     * {@inheritdoc}
     */
    public function writeLineFormatted($message): void
    {
        $this->symfonyOutput->writeln($message, SymfonyOutputInterface::OUTPUT_NORMAL);
    }

    public function writeRaw(string $message): void
    {
        $this->symfonyOutput->write($message, false, SymfonyOutputInterface::OUTPUT_RAW);
    }

    public function getStyle(): OutputStyleInterface
    {
        return $this->style;
    }

    public function isVerbose(): bool
    {
        return $this->symfonyOutput->isVerbose();
    }

    public function isDebug(): bool
    {
        return $this->symfonyOutput->isDebug();
    }
}
