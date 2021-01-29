<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\RulesetEngine;

class Error
{
    /**
     * @var string
     */
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function toString(): string
    {
        return $this->message;
    }
}
