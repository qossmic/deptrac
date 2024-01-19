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
use DEPTRAC_202401\Symfony\Contracts\EventDispatcher\Event;
/**
 * Allows to inspect input and output of a command.
 *
 * @author Francesco Levorato <git@flevour.net>
 */
class ConsoleEvent extends Event
{
    protected $command;
    private InputInterface $input;
    private OutputInterface $output;
    public function __construct(?Command $command, InputInterface $input, OutputInterface $output)
    {
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
    }
    /**
     * Gets the command that is executed.
     */
    public function getCommand() : ?Command
    {
        return $this->command;
    }
    /**
     * Gets the input instance.
     */
    public function getInput() : InputInterface
    {
        return $this->input;
    }
    /**
     * Gets the output instance.
     */
    public function getOutput() : OutputInterface
    {
        return $this->output;
    }
}
