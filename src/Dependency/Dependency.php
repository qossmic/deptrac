<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Dependency;

use Qossmic\Deptrac\AstRunner\AstMap\FileOccurrence;
use Qossmic\Deptrac\AstRunner\AstMap\TokenLikeName;

class Dependency implements DependencyInterface
{
    private TokenLikeName $tokenLikeNameA;
    private TokenLikeName $tokenLikeNameB;
    private FileOccurrence $fileOccurrence;

    public function __construct(TokenLikeName $tokenLikeNameA, TokenLikeName $tokenLikeNameB, FileOccurrence $fileOccurrence)
    {
        $this->tokenLikeNameA = $tokenLikeNameA;
        $this->tokenLikeNameB = $tokenLikeNameB;
        $this->fileOccurrence = $fileOccurrence;
    }

    public function getTokenLikeNameA(): TokenLikeName
    {
        return $this->tokenLikeNameA;
    }

    public function getTokenLikeNameB(): TokenLikeName
    {
        return $this->tokenLikeNameB;
    }

    public function getFileOccurrence(): FileOccurrence
    {
        return $this->fileOccurrence;
    }
}
