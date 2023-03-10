<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Analyser;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

interface ViolationCreatingInterface extends EventSubscriberInterface
{
    /**
     * @psalm-pure
     */
    public function ruleName(): string;

    /**
     * @psalm-pure
     */
    public function ruleDescription(): string;
}
