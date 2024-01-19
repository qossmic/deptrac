<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Console\Tester\Constraint;

use DEPTRAC_202401\PHPUnit\Framework\Constraint\Constraint;
use DEPTRAC_202401\Symfony\Component\Console\Command\Command;
final class CommandIsSuccessful extends Constraint
{
    public function toString() : string
    {
        return 'is successful';
    }
    protected function matches($other) : bool
    {
        return Command::SUCCESS === $other;
    }
    protected function failureDescription($other) : string
    {
        return 'the command ' . $this->toString();
    }
    protected function additionalFailureDescription($other) : string
    {
        $mapping = [Command::FAILURE => 'Command failed.', Command::INVALID => 'Command was invalid.'];
        return $mapping[$other] ?? \sprintf('Command returned exit status %d.', $other);
    }
}
