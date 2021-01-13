<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AstFileSyntaxErrorEvent extends Event
{
    private $file;

    private $syntaxError;

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
