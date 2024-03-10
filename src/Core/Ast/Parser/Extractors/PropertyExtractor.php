<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicTypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;
use Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser\PhpStanContainerDecorator;

/**
 * @implements ReferenceExtractorInterface<Property>
 */
class PropertyExtractor implements ReferenceExtractorInterface
{
    private readonly Lexer $lexer;
    private readonly PhpDocParser $docParser;

    public function __construct(
        private readonly PhpStanContainerDecorator $phpStanContainer,
        private readonly NikicTypeResolver $typeResolver
    ) {
        $this->lexer = new Lexer();
        $this->docParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
    }

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                foreach ($this->typeResolver->resolvePHPParserTypes($typeScope, $attribute->name) as $classLikeName) {
                    $referenceBuilder->attribute($classLikeName, $attribute->getLine());
                }
            }
        }
        if (null !== $node->type) {
            foreach ($this->typeResolver->resolvePropertyType($node->type) as $type) {
                $referenceBuilder->variable($type, $node->type->getStartLine());
            }
        }

        $docComment = $node->getDocComment();
        if ($docComment instanceof Doc) {
            $tokens = new TokenIterator($this->lexer->tokenize($docComment->getText()));
            $docNode = $this->docParser->parse($tokens);
            $templateTypes = array_merge(
                array_map(
                    static fn (TemplateTagValueNode $node): string => $node->name,
                    $docNode->getTemplateTagValues()
                ),
                $referenceBuilder->getTokenTemplates()
            );

            foreach ($docNode->getVarTagValues() as $tag) {
                $types = $this->typeResolver->resolvePHPStanDocParserType($tag->type, $typeScope, $templateTypes);

                foreach ($types as $type) {
                    $referenceBuilder->variable($type, $docComment->getStartLine());
                }
            }
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                $referenceBuilder->attribute($scope->resolveName($attribute->name), $attribute->getLine());
            }
        }
        if ($node->type instanceof Node\Name) {
            $referenceBuilder->variable($scope->resolveName($node->type), $node->type->getStartLine());
        }

        $docComment = $node->getDocComment();
        if ($docComment instanceof Doc) {
            $fileTypeMapper = $this->phpStanContainer->createFileTypeMapper();
            $function = $scope->getFunction();
            $resolvedPhpDoc = $fileTypeMapper->getResolvedPhpDoc(
                $scope->getFile(),
                $scope->getClassReflection()?->getName(),
                $scope->getTraitReflection()?->getName(),
                $function?->getName(),
                $docComment->getText(),
            );

            foreach ($resolvedPhpDoc->getVarTags() as $tag) {
                foreach (
                    $tag->getType()
                        ->getReferencedClasses() as $referencedClass
                ) {
                    $referenceBuilder->variable($referencedClass, $docComment->getStartLine());
                }
            }
        }
    }

    public function getNodeType(): string
    {
        return Property::class;
    }
}
