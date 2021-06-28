<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\AstRunner\AstMap\TokenName;

class Dependency implements DependencyInterface
{
    private TokenName $tokenNameA;
    private TokenName $tokenNameB;
    private FileOccurrence $fileOccurrence;

    public function __construct(TokenName $tokenNameA, TokenName $tokenNameB, FileOccurrence $fileOccurrence)
    {
        $this->tokenNameA = $tokenNameA;
        $this->tokenNameB = $tokenNameB;
        $this->fileOccurrence = $fileOccurrence;
    }

    public function getTokenNameA(): TokenName
    {
        return $this->tokenNameA;
    }

    public function getTokenNameB(): TokenName
    {
        return $this->tokenNameB;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->fileOccurrence;
    }
}
