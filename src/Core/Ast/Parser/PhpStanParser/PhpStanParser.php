<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser;

use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeTraverser;
use PHPStan\Parser\Parser;
use PHPStan\Parser\ParserErrorsException;
use Qossmic\Deptrac\Contract\Ast\CouldNotParseFileException;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceCacheInterface;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ReferenceExtractorInterface;
use Qossmic\Deptrac\Core\Ast\Parser\ParserInterface;

class PhpStanParser implements ParserInterface
{
    private Parser $parser;
    private readonly NodeTraverser $traverser;

    /**
     * @param ReferenceExtractorInterface<\PhpParser\Node>[] $extractors
     */
    public function __construct(
        private readonly PhpStanContainerDecorator $phpStanContainer,
        private readonly AstFileReferenceCacheInterface $cache,
        private readonly iterable $extractors
    ) {
        $this->traverser = new NodeTraverser();
        $this->parser = $this->phpStanContainer->createPHPStanParser();
    }

    public function parseFile(string $file): FileReference
    {
        if (null !== $fileReference = $this->cache->get($file)) {
            return $fileReference;
        }

        try {
            $scopeFactory = $this->phpStanContainer->createScopeFactory();
            $reflectionProvider = $this->phpStanContainer->createReflectionProvider();

            $fileReferenceBuilder = FileReferenceBuilder::create($file);
            $visitor = new FileReferenceVisitor($fileReferenceBuilder, $scopeFactory, $reflectionProvider, $file, ...$this->extractors);
            $nodes = $this->parser->parseFile($file);
            $this->traverser->addVisitor($visitor);
            $this->traverser->traverse($nodes);
            $this->traverser->removeVisitor($visitor);

            return $fileReferenceBuilder->build();
        } catch (ParserErrorsException $exception) {
            throw CouldNotParseFileException::because($exception->getMessage(), $exception);
        }
    }

    public function getNodeForClassLikeReference(ClassLikeReference $classReference): ?ClassLike
    {
        return null;
    }
}
