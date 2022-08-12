<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast\AstMap\File;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileToken;

final class FileTokenTest extends TestCase
{
    public function testPathNormalization(): void
    {
        $fileName = new FileToken('/path/to/file.php');
        $this->assertSame('/path/to/file.php', $fileName->path);
        $this->assertSame('/path/to/file.php', $fileName->toString());

        $fileName = new FileToken('\\path\\to\\file.php');
        $this->assertSame('/path/to/file.php', $fileName->path);
        $this->assertSame('/path/to/file.php', $fileName->toString());
    }
}
