<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\File\FileHelper;
use Qossmic\Deptrac\PathNameFilterIterator;
use SplFileInfo;

final class PathNameFilterIteratorTest extends TestCase
{
    /**
     * @dataProvider getTestFilterData
     */
    public function testFilter(\Iterator $inner, array $matchPatterns, array $noMatchPatterns, array $resultArray): void
    {
        $iterator = new PathNameFilterIterator($inner, $matchPatterns, $noMatchPatterns);

        $values = array_map(
            static function (SplFileInfo $fileInfo) {
                return $fileInfo->getPathname();
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
            'A'.FileHelper::DIR_SEPARATOR.'B'.FileHelper::DIR_SEPARATOR.'C'.FileHelper::DIR_SEPARATOR.'abc.dat'
        );

        //PATH:   A/B/ab.dat
        $inner[] = new SplFileInfo('A'.FileHelper::DIR_SEPARATOR.'B'.FileHelper::DIR_SEPARATOR.'ab.dat');

        //PATH:   A/a.dat
        $inner[] = new SplFileInfo('A'.FileHelper::DIR_SEPARATOR.'a.dat');

        //PATH:   copy/A/B/C/abc.dat.copy
        $inner[] = new SplFileInfo(
            'copy'.FileHelper::DIR_SEPARATOR.'A'.FileHelper::DIR_SEPARATOR.'B'.FileHelper::DIR_SEPARATOR.'C'.FileHelper::DIR_SEPARATOR.'abc.dat.copy'
        );

        //PATH:   copy/A/B/ab.dat.copy
        $inner[] = new SplFileInfo(
            'copy'.FileHelper::DIR_SEPARATOR.'A'.FileHelper::DIR_SEPARATOR.'B'.FileHelper::DIR_SEPARATOR.'ab.dat.copy'
        );

        //PATH:   copy/A/a.dat.copy
        $inner[] = new SplFileInfo('copy'.FileHelper::DIR_SEPARATOR.'A'.FileHelper::DIR_SEPARATOR.'a.dat.copy');

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
