<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\AstRunner\AstMap;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;

final class FileNameTest extends TestCase
{
    public function testPathNormalization(): void
    {
        $fileName = new AstMap\FileName('/path/to/file.php');
        $this->assertSame('/path/to/file.php', $fileName->getFilepath());
        $this->assertSame('/path/to/file.php', $fileName->toString());
        
        $fileName = new AstMap\FileName('\\path\\to\\file.php');
        $this->assertSame('/path/to/file.php', $fileName->getFilepath());
        $this->assertSame('/path/to/file.php', $fileName->toString());
    }
}
