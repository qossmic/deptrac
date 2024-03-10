<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ClassLikeExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\UseExtractor;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicTypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\ParserInterface;
use Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser\PhpStanContainerDecorator;
use Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser\PhpStanParser;
use stdClass;
use TypeError;

final class ParserTest extends TestCase
{
    /**
     * @dataProvider createParser
     */
    public function testParseWithInvalidData(ParserInterface $parser): void
    {
        $this->expectException(TypeError::class);
        $parser->parseFile(new stdClass());
    }

    /**
     * @dataProvider createParser
     */
    public function testParseDoesNotIgnoreUsesByDefault(ParserInterface $parser): void
    {
        $filePath = __DIR__.'/Fixtures/CountingUseStatements.php';
        self::assertCount(1, $parser->parseFile($filePath)->dependencies);
    }

    /**
     * @requires PHP >= 8.0
     *
     * @dataProvider createParser
     */
    public function testParseAttributes(ParserInterface $parser): void
    {
        $filePath = __DIR__.'/Fixtures/Attributes.php';
        $astFileReference = $parser->parseFile($filePath);
        $astClassReferences = $astFileReference->classLikeReferences;
        self::assertCount(7, $astClassReferences[0]->dependencies);
        self::assertCount(2, $astClassReferences[1]->dependencies);
        self::assertCount(1, $astClassReferences[2]->dependencies);
    }

    /**
     * @dataProvider createParser
     */
    public function testParseTemplateTypes(ParserInterface $parser): void
    {
        $filePath = __DIR__.'/Fixtures/TemplateTypes.php';
        $astFileReference = $parser->parseFile($filePath);
        $astClassReferences = $astFileReference->classLikeReferences;
        self::assertCount(0, $astClassReferences[0]->dependencies);
    }

    /**
     * @dataProvider createParser
     */
    public function testParseClassDocTags(ParserInterface $parser): void
    {
        $filePath = __DIR__.'/Fixtures/DocTags.php';
        $astFileReference = $parser->parseFile($filePath);

        self::assertCount(2, $astFileReference->classLikeReferences);
        $classesByName = $this->refsByName($astFileReference->classLikeReferences);

        $this->assertSame(
            [
                '@internal' => [''],
                '@note' => ['Note one', 'Note two'],
            ],
            $classesByName['TaggedThing']->tags
        );
        $this->assertSame([], $classesByName['UntaggedThing']->tags);
    }

    /**
     * @dataProvider createParser
     */
    public function testParseFunctionDocTags(ParserInterface $parser): void
    {
        $filePath = __DIR__.'/Fixtures/Functions.php';
        $astFileReference = $parser->parseFile($filePath);

        self::assertCount(2, $astFileReference->functionReferences);
        $functionsByName = $this->refsByName($astFileReference->functionReferences);

        $this->assertSame(
            ['@param' => ['string $foo', 'string $bar']],
            $functionsByName['taggedFunction()']->tags
        );
        $this->assertSame([], $functionsByName['untaggedFunction()']->tags);
    }

    private function refsByName(array $refs): array
    {
        $refsByName = [];

        foreach ($refs as $ref) {
            $name = preg_replace('/^.*\\\\(\w+(\(\))?)$/', '$1', $ref->getToken()->toString());
            $refsByName[$name] = $ref;
        }

        return $refsByName;
    }

    /**
     * @return list<array{ParserInterface}>
     */
    public static function createParser(): array
    {
        $typeResolver = new NikicTypeResolver();
        $phpStanContainer = new PhpStanContainerDecorator('', []);

        $cache = new AstFileReferenceInMemoryCache();
        $extractors = [
            new UseExtractor(),
            new ClassLikeExtractor($typeResolver),
        ];
        $nikicPhpParser = new NikicPhpParser(
            (new ParserFactory())->create(
                ParserFactory::ONLY_PHP7,
                new Lexer()
            ), $cache, $extractors
        );

        $phpstanParser = new PhpStanParser($phpStanContainer, $cache, $extractors);

        return [
            'Nikic Parser' => [$nikicPhpParser],
            'PHPStan Parser' => [$phpstanParser],
        ];
    }
}
