<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser;

use PhpParser\Node\Stmt\ClassLike;
use Qossmic\Deptrac\Contract\Ast\CouldNotParseFileException;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;

interface ParserInterface
{
    /**
     * @throws CouldNotParseFileException
     */
    public function parseFile(string $file): FileReference;

    /**
     * @throws CouldNotParseFileException
     */
    public function getNodeForClassLikeReference(ClassLikeReference $classReference): ?ClassLike;

}
