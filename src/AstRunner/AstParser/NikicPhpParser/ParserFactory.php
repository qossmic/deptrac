<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Lexer;
use PhpParser\Parser;

class ParserFactory
{
    public static function createParser(): Parser
    {
        return (new \PhpParser\ParserFactory())->create(\PhpParser\ParserFactory::PREFER_PHP7, new Lexer());
    }
}
