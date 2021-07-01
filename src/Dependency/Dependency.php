<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\AstRunner\AstMap\TokenName;

class Dependency implements DependencyInterface
{
    private TokenName $dependant;
    private TokenName $dependee;
    private FileOccurrence $fileOccurrence;

    public function __construct(TokenName $dependant, TokenName $dependee, FileOccurrence $fileOccurrence)
    {
        $this->dependant = $dependant;
        $this->dependee = $dependee;
        $this->fileOccurrence = $fileOccurrence;
    }

    public function getDependant(): TokenName
    {
        return $this->dependant;
    }

    public function getDependee(): TokenName
    {
        return $this->dependee;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->fileOccurrence;
    }
}
