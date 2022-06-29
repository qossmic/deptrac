<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReference;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\Cache\AstFileReferenceCacheInterface;
use Qossmic\Deptrac\Core\Ast\Parser\ParserInterface;
use Qossmic\Deptrac\Core\Ast\Parser\ReferenceExtractorInterface;
use Qossmic\Deptrac\Core\Ast\Parser\TypeResolver;
use Qossmic\Deptrac\Supportive\File\FileReader;
use Qossmic\Deptrac\Supportive\ShouldNotHappenException;

class NikicPhpParser implements ParserInterface
{
    /**
     * @var array<string, ClassLike>
     */
    private static array $classAstMap = [];

    private Parser $parser;

    private AstFileReferenceCacheInterface $cache;

    private TypeResolver $typeResolver;

    /**
     * @var ReferenceExtractorInterface[]
     */
    private iterable $extractors;

    private NodeTraverser $traverser;

    /**
     * @param ReferenceExtractorInterface[] $extractors
     */
    public function __construct(
        Parser $parser,
        AstFileReferenceCacheInterface $cache,
        TypeResolver $typeResolver,
        iterable $extractors
    ) {
        $this->parser = $parser;
        $this->cache = $cache;
        $this->typeResolver = $typeResolver;
        $this->extractors = $extractors;

        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NameResolver());
    }

    public function parseFile(string $file): FileReference
    {
        if (null !== $fileReference = $this->cache->get($file)) {
            return $fileReference;
        }

        $fileReferenceBuilder = FileReferenceBuilder::create($file);
        $nodes = $this->parser->parse(FileReader::read($file));
        if (null === $nodes) {
            throw new ShouldNotHappenException();
        }

        $visitor = new FileReferenceVisitor($fileReferenceBuilder, $this->typeResolver, ...$this->extractors);
        $this->traverser->addVisitor($visitor);
        $this->traverser->traverse($nodes);
        $this->traverser->removeVisitor($visitor);

        $fileReference = $fileReferenceBuilder->build();
        $this->cache->set($fileReference);

        return $fileReference;
    }

    public function getNodeForClassLikeReference(ClassLikeReference $classReference): ?ClassLike
    {
        $classLikeName = $classReference->getToken()->toString();

        if (isset(self::$classAstMap[$classLikeName])) {
            return self::$classAstMap[$classLikeName];
        }

        $astFileReference = $classReference->getFileReference();

        if (null === $astFileReference) {
            return null;
        }

        $findingVisitor = new FindingVisitor(static fn (Node $node): bool => $node instanceof ClassLike);

        $nodes = $this->parser->parse(FileReader::read($astFileReference->getFilepath()));
        if (null === $nodes) {
            throw new ShouldNotHappenException();
        }

        $this->traverser->addVisitor($findingVisitor);
        $this->traverser->traverse($nodes);
        $this->traverser->removeVisitor($findingVisitor);

        /** @var ClassLike[] $classLikeNodes */
        $classLikeNodes = $findingVisitor->getFoundNodes();

        foreach ($classLikeNodes as $classLikeNode) {
            if (isset($classLikeNode->namespacedName)) {
                $namespacedName = $classLikeNode->namespacedName;
                $className = $namespacedName->toCodeString();
            } elseif ($classLikeNode->name instanceof Identifier) {
                $className = $classLikeNode->name->toString();
            } else {
                continue;
            }

            self::$classAstMap[$className] = $classLikeNode;
        }

        /** @psalm-var ?ClassLike */
        return self::$classAstMap[$classLikeName] ?? null;
    }
}
