<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser;

use PhpParser\Error;
use PhpParser\ErrorHandler\Throwing;
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
use Qossmic\Deptrac\Core\Ast\Parser\CouldNotParseFileException;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ReferenceExtractorInterface;
use Qossmic\Deptrac\Core\Ast\Parser\ParserInterface;
use Qossmic\Deptrac\Core\Ast\Parser\TypeResolver;
use Qossmic\Deptrac\Supportive\File\Exception\CouldNotReadFileException;
use Qossmic\Deptrac\Supportive\File\FileReader;

class NikicPhpParser implements ParserInterface
{
    /**
     * @var array<string, ClassLike>
     */
    private static array $classAstMap = [];

    private readonly NodeTraverser $traverser;

    /**
     * @param ReferenceExtractorInterface[] $extractors
     */
    public function __construct(
        private readonly Parser $parser,
        private readonly AstFileReferenceCacheInterface $cache,
        private readonly TypeResolver $typeResolver,
        private readonly iterable $extractors
    ) {
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NameResolver());
    }

    public function parseFile(string $file): FileReference
    {
        try {
            if (null !== $fileReference = $this->cache->get($file)) {
                return $fileReference;
            }

            $fileReferenceBuilder = FileReferenceBuilder::create($file);
            $fileContents = FileReader::read($file);
            /** @throws Error */
            $nodes = $this->parser->parse($fileContents, new Throwing());
        } catch (Error|CouldNotReadFileException $e) {
            throw CouldNotParseFileException::because($e->getMessage(), $e);
        }
        /** @var array<Node> $nodes */
        $visitor = new FileReferenceVisitor($fileReferenceBuilder, $this->typeResolver, ...$this->extractors);
        $this->traverser->addVisitor($visitor);
        $this->traverser->traverse($nodes);
        $this->traverser->removeVisitor($visitor);

        $fileReference = $fileReferenceBuilder->build();
        $this->cache->set($fileReference);

        return $fileReference;
    }

    /**
     * @throws CouldNotParseFileException
     */
    public function getNodeForClassLikeReference(ClassLikeReference $classReference): ?ClassLike
    {
        $classLikeName = $classReference->getToken()->toString();

        if (isset(self::$classAstMap[$classLikeName])) {
            return self::$classAstMap[$classLikeName];
        }

        $filepath = $classReference->getFilepath();

        if (null === $filepath) {
            return null;
        }

        $findingVisitor = new FindingVisitor(static fn (Node $node): bool => $node instanceof ClassLike);

        try {
            $fileContents = FileReader::read($filepath);
            /** @throws Error */
            $nodes = $this->parser->parse($fileContents, new Throwing());
        } catch (Error|CouldNotReadFileException $e) {
            throw CouldNotParseFileException::because($e->getMessage(), $e);
        }
        /** @var array<Node> $nodes */
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
