<?php

namespace SensioLabs\Deptrac\AstRunner\Event;

use Symfony\Component\EventDispatcher\Event;

class AstFileSyntaxErrorEvent extends Event
{
    private $file;

    private $syntaxError;

    public function __construct(\SplFileInfo $file, string $syntaxError)
    {
        $this->file = $file;
        $this->syntaxError = $syntaxError;
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    public function getSyntaxError(): string
    {
        return $this->syntaxError;
    }
}
