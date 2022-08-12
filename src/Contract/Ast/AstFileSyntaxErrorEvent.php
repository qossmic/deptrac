<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Contract\Ast;

use Symfony\Contracts\EventDispatcher\Event;

class AstFileSyntaxErrorEvent extends Event
{
    public function __construct(private readonly string $file, private readonly string $syntaxError)
    {
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getSyntaxError(): string
    {
        return $this->syntaxError;
    }
}
