<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Core\Ast;

use LogicException;
use PhpParser\Error;
use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Contract\Ast\AstFileAnalysedEvent;
use Qossmic\Deptrac\Contract\Ast\AstFileSyntaxErrorEvent;
use Qossmic\Deptrac\Contract\Ast\PostCreateAstMapEvent;
use Qossmic\Deptrac\Contract\Ast\PreCreateAstMapEvent;
use Qossmic\Deptrac\Core\Ast\AstLoader;
use Qossmic\Deptrac\Core\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeToken;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceInMemoryCache;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;
use Qossmic\Deptrac\Core\Ast\Parser\ParserInterface;
use Qossmic\Deptrac\Core\Ast\Parser\TypeResolver;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicInheritance\FixtureBasicInheritanceWithNoiseA;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicInheritance\FixtureBasicInheritanceWithNoiseB;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicInheritance\FixtureBasicInheritanceWithNoiseC;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceA;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceB;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceC;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceD;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceE;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceA;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceB;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceC;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceD;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceE;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA1;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA2;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceB;
use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceC;

final class AstMapFlattenGeneratorTest extends TestCase
{
    use ArrayAssertionTrait;

    private TraceableEventDispatcher $eventDispatcher;
    private AstLoader $astLoader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventDispatcher = new TraceableEventDispatcher(
            new EventDispatcher(),
            new Stopwatch()
        );
        $this->astLoader = new AstLoader(
            new NikicPhpParser(
                (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer()),
                new AstFileReferenceInMemoryCache(),
                new TypeResolver(),
                []
            ),
            $this->eventDispatcher
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->astLoader);
        unset($this->eventDispatcher);
    }

    private function getAstMap(string $fixture): AstMap
    {
        return $this->astLoader->createAstMap([__DIR__.'/Fixtures/BasicInheritance/'.$fixture.'.php']);
    }

    private function getInheritedInherits(string $class, AstMap $astMap): array
    {
        $inherits = [];
        foreach ($astMap->getClassInherits(ClassLikeToken::fromFQCN($class)) as $v) {
            if (count($v->getPath()) > 0) {
                $inherits[] = (string) $v;
            }
        }

        return $inherits;
    }

    public function testBasicInheritance(): void
    {
        $expectedEvents = [
            PreCreateAstMapEvent::class,
            AstFileAnalysedEvent::class,
            PostCreateAstMapEvent::class,
        ];

        $astMap = $this->getAstMap('FixtureBasicInheritance');

        $dispatchedEvents = $this->eventDispatcher->getOrphanedEvents();
        self::assertSame($expectedEvents, $dispatchedEvents);

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceA::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceB::class, $astMap)
        );

        self::assertArrayValuesEquals(
            ['Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceA::6 (Extends) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceB::7 (Extends))'],
            $this->getInheritedInherits(FixtureBasicInheritanceC::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceA::6 (Extends) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceC::8 (Extends) -> Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceB::7 (Extends))',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceB::7 (Extends) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceC::8 (Extends))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceD::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceA::6 (Extends) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceD::9 (Extends) -> Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceC::8 (Extends) -> Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceB::7 (Extends))',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceB::7 (Extends) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceD::9 (Extends) -> Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceC::8 (Extends))',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceC::8 (Extends) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceD::9 (Extends))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceE::class, $astMap)
        );
    }

    public function testBasicInheritanceInterfaces(): void
    {
        $expectedEvents = [
            PreCreateAstMapEvent::class,
            AstFileAnalysedEvent::class,
            PostCreateAstMapEvent::class,
        ];

        $astMap = $this->getAstMap('FixtureBasicInheritanceInterfaces');

        $dispatchedEvents = $this->eventDispatcher->getOrphanedEvents();
        self::assertSame($expectedEvents, $dispatchedEvents);

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceA::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceB::class, $astMap)
        );

        self::assertArrayValuesEquals(
            ['Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceA::6 (Implements) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Implements))'],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceC::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceA::6 (Implements) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Implements) -> Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Implements))',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Implements) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Implements))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceD::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceA::6 (Implements) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceD::9 (Implements) -> Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Implements) -> Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Implements))',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceB::7 (Implements) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceD::9 (Implements) -> Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Implements))',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceC::8 (Implements) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceInterfaceD::9 (Implements))',
            ],
            $this->getInheritedInherits(FixtureBasicInheritanceInterfaceE::class, $astMap)
        );
    }

    public function testBasicMultipleInheritanceInterfaces(): void
    {
        $expectedEvents = [
            PreCreateAstMapEvent::class,
            AstFileAnalysedEvent::class,
            PostCreateAstMapEvent::class,
        ];

        $astMap = $this->getAstMap('MultipleInheritanceInterfaces');

        $dispatchedEvents = $this->eventDispatcher->getOrphanedEvents();
        self::assertSame($expectedEvents, $dispatchedEvents);

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(MultipleInteritanceA1::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(MultipleInteritanceA2::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(MultipleInteritanceA::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA1::7 (Implements) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA::8 (Implements))',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA2::7 (Implements) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA::8 (Implements))',
            ],
            $this->getInheritedInherits(MultipleInteritanceB::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA1::7 (Implements) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceB::9 (Implements) -> Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA::8 (Implements))',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA1::8 (Implements) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceB::9 (Implements))',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA2::7 (Implements) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceB::9 (Implements) -> Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA::8 (Implements))',
                'Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceA::8 (Implements) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\MultipleInteritanceB::9 (Implements))',
            ],
            $this->getInheritedInherits(MultipleInteritanceC::class, $astMap)
        );
    }

    public function testBasicMultipleInheritanceWithNoise(): void
    {
        $expectedEvents = [
            PreCreateAstMapEvent::class,
            AstFileAnalysedEvent::class,
            PostCreateAstMapEvent::class,
        ];

        $astMap = $this->getAstMap('FixtureBasicInheritanceWithNoise');

        $dispatchedEvents = $this->eventDispatcher->getOrphanedEvents();
        self::assertSame($expectedEvents, $dispatchedEvents);

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceWithNoiseA::class, $astMap)
        );

        self::assertArrayValuesEquals(
            [],
            $this->getInheritedInherits(FixtureBasicInheritanceWithNoiseB::class, $astMap)
        );

        self::assertArrayValuesEquals(
            ['Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicInheritance\FixtureBasicInheritanceWithNoiseA::18 (Extends) (path: Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicInheritance\FixtureBasicInheritanceWithNoiseB::19 (Extends))'],
            $this->getInheritedInherits(FixtureBasicInheritanceWithNoiseC::class, $astMap)
        );
    }

    public function testSkipsErrorsAndDisptachesErrorEventAndReturnsEmptyAstMap(): void
    {
        $expectedEvents = [
            PreCreateAstMapEvent::class,
            AstFileSyntaxErrorEvent::class,
            PostCreateAstMapEvent::class,
        ];
        $parser = $this->createMock(ParserInterface::class);
        $astLoader = new AstLoader($parser, $this->eventDispatcher);

        $parser
            ->expects(self::atLeastOnce())
            ->method('parseFile')
            ->with(__DIR__.'/Fixtures/BasicInheritance/FixtureBasicInheritanceWithNoise.php')
            ->willThrowException(new Error('Syntax Error'));

        $astLoader->createAstMap([__DIR__.'/Fixtures/BasicInheritance/FixtureBasicInheritanceWithNoise.php']);

        $dispatchedEvents = $this->eventDispatcher->getOrphanedEvents();
        self::assertSame($expectedEvents, $dispatchedEvents);
    }

    public function testThrowsOtherExceptions(): void
    {
        $parser = $this->createMock(ParserInterface::class);
        $astLoader = new AstLoader($parser, $this->eventDispatcher);

        $parser
            ->expects(self::atLeastOnce())
            ->method('parseFile')
            ->with(__DIR__.'/Fixtures/BasicInheritance/FixtureBasicInheritanceWithNoise.php')
            ->willThrowException(new LogicException('Uncaught exception'));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Uncaught exception');

        $astLoader->createAstMap([__DIR__.'/Fixtures/BasicInheritance/FixtureBasicInheritanceWithNoise.php']);
    }
}
