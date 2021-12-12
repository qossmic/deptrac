<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\File\FileHelper;
use Qossmic\Deptrac\PathNameFilterIterator;
use SplFileInfo;
use staabm\PHPUnitCrossOs\Comparator\DirSeparatorAgnosticString;

final class PathNameFilterIteratorTest extends TestCase
{
    use CrossOsAgnosticEqualsTrait;
    
    /**
     * @dataProvider getTestFilterData
     */
    public function testFilter(\Iterator $inner, array $matchPatterns, array $noMatchPatterns, array $resultArray): void
    {
        $iterator = new PathNameFilterIterator($inner, $matchPatterns, $noMatchPatterns);

        $values = array_map(
            static function (SplFileInfo $fileInfo) {
                return (new DirSeparatorAgnosticString($fileInfo->getPathname()))->getNormalized();
            },
            iterator_to_array($iterator, false)
        );

        sort($values);
        sort($resultArray);

        self::assertEquals($resultArray, array_values($values));
    }

    public function getTestFilterData(): array
    {
        $inner = new ArrayIterator();

        //PATH:   A/B/C/abc.dat
        $inner[] = new SplFileInfo(
            'A/B/C/abc.dat'
        );

        //PATH:   A/B/ab.dat
        $inner[] = new SplFileInfo('A/B/ab.dat');

        //PATH:   A/a.dat
        $inner[] = new SplFileInfo('A/a.dat');

        //PATH:   copy/A/B/C/abc.dat.copy
        $inner[] = new SplFileInfo(
            'copy/A/B/C/abc.dat.copy'
        );

        //PATH:   copy/A/B/ab.dat.copy
        $inner[] = new SplFileInfo(
            'copy/A/B/ab.dat.copy'
        );

        //PATH:   copy/A/a.dat.copy
        $inner[] = new SplFileInfo('copy/A/a.dat.copy');

        return [
            [$inner, ['/^A/'], [], ['A/B/C/abc.dat', 'A/B/ab.dat', 'A/a.dat']],
            [$inner, ['/^A\/B/'], [], ['A/B/C/abc.dat', 'A/B/ab.dat']],
            [$inner, ['/^A\/B\/C/'], [], ['A/B/C/abc.dat']],
            [$inner, ['/A\/B\/C/'], [], ['A/B/C/abc.dat', 'copy/A/B/C/abc.dat.copy']],

            [$inner, ['A'], [], ['A/B/C/abc.dat', 'A/B/ab.dat', 'A/a.dat', 'copy/A/B/C/abc.dat.copy', 'copy/A/B/ab.dat.copy', 'copy/A/a.dat.copy']],
            [$inner, ['A/B'], [], ['A/B/C/abc.dat', 'A/B/ab.dat', 'copy/A/B/C/abc.dat.copy', 'copy/A/B/ab.dat.copy']],
            [$inner, ['A/B/C'], [], ['A/B/C/abc.dat', 'copy/A/B/C/abc.dat.copy']],

            [$inner, ['copy/A'], [], ['copy/A/B/C/abc.dat.copy', 'copy/A/B/ab.dat.copy', 'copy/A/a.dat.copy']],
            [$inner, ['copy/A/B'], [], ['copy/A/B/C/abc.dat.copy', 'copy/A/B/ab.dat.copy']],
            [$inner, ['copy/A/B/C'], [], ['copy/A/B/C/abc.dat.copy']],
        ];
    }
}
