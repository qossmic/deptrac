<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Events\Ast;

use Symfony\Contracts\EventDispatcher\Event;

class AstFileSyntaxErrorEvent extends Event
{
    private string $file;

    private string $syntaxError;

    public function __construct(string $file, string $syntaxError)
    {
        $this->file = $file;
        $this->syntaxError = $syntaxError;
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
