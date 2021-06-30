<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AstFileAnalyzedEvent extends Event
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function getFile(): string
    {
        return $this->file;
    }
}
