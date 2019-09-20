<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node\Stmt;
use PhpParser\Parser;

class FileParser
{
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return Stmt[]
     */
    public function parse(\SplFileInfo $data): array
    {
        return (array) $this->parser->parse(
            (string) file_get_contents($data->getPathname())
        );
    }
}
