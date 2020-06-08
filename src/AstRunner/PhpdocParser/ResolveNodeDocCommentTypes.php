<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner\PhpdocParser;

use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeAbstract;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use SensioLabs\Deptrac\AstRunner\AstMap\ClassReferenceBuilder;

final class ResolveNodeDocCommentTypes
{
    private $resolveTypeNames;
    private $lexer;
    private $docParser;

    public function __construct()
    {
        $this->resolveTypeNames = new ResolveTypeNames();
        $this->lexer = new Lexer();
        $this->docParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
    }

    public function __invoke(ClassReferenceBuilder $builder, NodeAbstract $node, Context $context): void
    {
        if (null === $docComment = $node->getDocComment()) {
            return;
        }

        $tokens = new TokenIterator($this->lexer->tokenize($docComment->getText()));
        $docNode = $this->docParser->parse($tokens);

        foreach ($docNode->getParamTagValues() as $tag) {
            $types = ($this->resolveTypeNames)($tag->type, $context);

            foreach ($types as $type) {
                $builder->parameter($type, $docComment->getStartLine());
            }
        }

        foreach ($docNode->getVarTagValues() as $tag) {
            $types = ($this->resolveTypeNames)($tag->type, $context);

            foreach ($types as $type) {
                if ($node instanceof Property) {
                    $builder->property($type, $docComment->getStartLine());
                } else {
                    $builder->variable($type, $docComment->getStartLine());
                }
            }
        }

        foreach ($docNode->getReturnTagValues() as $tag) {
            $types = ($this->resolveTypeNames)($tag->type, $context);

            foreach ($types as $type) {
                $builder->returnType($type, $docComment->getStartLine());
            }
        }

        foreach ($docNode->getThrowsTagValues() as $tag) {
            $types = ($this->resolveTypeNames)($tag->type, $context);

            foreach ($types as $type) {
                $builder->throwStatement($type, $docComment->getStartLine());
            }
        }
    }
}
