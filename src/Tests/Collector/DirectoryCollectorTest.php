<?php

namespace SensioLabs\Deptrac\Tests\Collector;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\AstFileReference;
use SensioLabs\Deptrac\Collector\DirectoryCollector;
use SensioLabs\Deptrac\CollectorFactory;

class DirectoryCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testType()
    {
        $this->assertEquals('directory', (new DirectoryCollector())->getType());
    }

    public function dataProviderStatisfy()
    {
        yield [['regex' => 'foo/layer1/.*'], 'foo/layer1/bar.php', true];
        yield [['regex' => 'foo/layer1/.*'], 'foo/layer1/dir/bar.php', true];
        yield [['regex' => 'foo/layer1/.*'], 'foo/layer2/bar.php', false];
    }

    /**
     * @dataProvider dataProviderStatisfy
     */
    public function testSatisfy($configuration, $filePath, $expected)
    {
        $fileReference = $this->prophesize(AstFileReference::class);
        $fileReference->getFilepath()->willReturn($filePath);

        $astClassReference = $this->prophesize(AstClassReferenceInterface::class);
        $astClassReference->getFileReference()->willReturn($fileReference->reveal());

        $stat = (new DirectoryCollector())->satisfy(
            $configuration,
            $astClassReference->reveal(),
            $this->prophesize(AstMap::class)->reveal(),
            $this->prophesize(CollectorFactory::class)->reveal(),
            $this->prophesize(AstParserInterface::class)->reveal()
        );

        $this->assertEquals($expected, $stat);
    }
}
