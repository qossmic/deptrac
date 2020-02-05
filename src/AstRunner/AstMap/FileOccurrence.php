<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstMap;

final class FileOccurrence
{
    /**
     * @var AstFileReference
     */
    private $fileReference;
    /**
     * @var int
     */
    private $line;

    public function __construct(AstFileReference $fileReference, int $line)
    {
        $this->fileReference = $fileReference;
        $this->line = $line;
    }

    public function getFilenpath(): string
    {
        return $this->fileReference->getFilepath();
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
