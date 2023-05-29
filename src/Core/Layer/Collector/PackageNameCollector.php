<?php

namespace Qossmic\Deptrac\Core\Layer\Collector;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Qossmic\Deptrac\Contract\Ast\CouldNotParseFileException;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicPhpParser;

class PackageNameCollector extends RegexCollector
{
    private readonly Lexer $lexer;
    private readonly PhpDocParser $docParser;

    public function __construct(
        private readonly NikicPhpParser $nikicPhpParser
    ) {
        $this->lexer = new Lexer();
        $this->docParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
    }

    public function satisfy(array $config, TokenReferenceInterface $reference): bool
    {
        if (!$reference instanceof ClassLikeReference) {
            return false;
        }

        $regex = $this->getValidatedPattern($config);

        foreach ($this->getPackages($reference) as $package) {
            if (1 === preg_match($regex, $package)) {
                return true;
            }
        }

        return false;
    }

    protected function getPattern(array $config): string
    {
        if (!isset($config['value']) || !is_string($config['value'])) {
            throw new InvalidCollectorDefinitionException('PackageNameCollector needs the value configuration.');
        }

        return '/'.$config['value'].'/im';
    }

    /**
     * @return array<string>
     *
     * @throws CouldNotParseFileException
     */
    private function getPackages(ClassLikeReference $reference): array
    {
        $docBlock = $this->getCommentDoc($reference);

        if (!$docBlock) {
            return [];
        }

        $tokens = new TokenIterator($this->lexer->tokenize($docBlock));
        $docNode = $this->docParser->parse($tokens);

        return array_map(
            static fn (PhpDocTagNode $node) => (string) $node->value,
            $docNode->getTagsByName('@package')
        );
    }

    /**
     * @throws CouldNotParseFileException
     */
    private function getCommentDoc(ClassLikeReference $reference): string
    {
        $node = $this->nikicPhpParser->getNodeForClassLikeReference($reference);

        if (null === $node) {
            return '';
        }

        $doc = $node->getDocComment();

        if (null === $doc) {
            return '';
        }

        return $doc->getText();
    }
}
