<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Console\Event;

use DEPTRAC_202401\Symfony\Component\Console\Command\Command;
use DEPTRAC_202401\Symfony\Component\Console\Input\InputInterface;
use DEPTRAC_202401\Symfony\Component\Console\Output\OutputInterface;
/**
 * Allows to manipulate the exit code of a command after its execution.
 *
 * @author Francesco Levorato <git@flevour.net>
 * @author Jules Pietri <jules@heahprod.com>
 */
final class ConsoleTerminateEvent extends ConsoleEvent
{
    public function __construct(Command $command, InputInterface $input, OutputInterface $output, private int $exitCode, private readonly ?int $interruptingSignal = null)
    {
        parent::__construct($command, $input, $output);
    }
    public function setExitCode(int $exitCode) : void
    {
        $this->exitCode = $exitCode;
    }
    public function getExitCode() : int
    {
        return $this->exitCode;
    }
    public function getInterruptingSignal() : ?int
    {
        return $this->interruptingSignal;
    }
}
