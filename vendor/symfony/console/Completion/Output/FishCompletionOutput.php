<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Console\Completion\Output;

use DEPTRAC_202401\Symfony\Component\Console\Completion\CompletionSuggestions;
use DEPTRAC_202401\Symfony\Component\Console\Output\OutputInterface;
/**
 * @author Guillaume Aveline <guillaume.aveline@pm.me>
 */
class FishCompletionOutput implements CompletionOutputInterface
{
    public function write(CompletionSuggestions $suggestions, OutputInterface $output) : void
    {
        $values = $suggestions->getValueSuggestions();
        foreach ($suggestions->getOptionSuggestions() as $option) {
            $values[] = '--' . $option->getName();
            if ($option->isNegatable()) {
                $values[] = '--no-' . $option->getName();
            }
        }
        $output->write(\implode("\n", $values));
    }
}
