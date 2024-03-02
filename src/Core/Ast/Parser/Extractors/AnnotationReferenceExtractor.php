<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;
use Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser\PhpStanContainerDecorator;

class AnnotationReferenceExtractor implements ReferenceExtractorInterface
{
    private readonly Lexer $lexer;
    private readonly PhpDocParser $docParser;

    public function __construct(
        private readonly PhpStanContainerDecorator $phpStanContainer,
        private readonly TypeResolver $typeResolver
    ) {
        $this->lexer = new Lexer();
        $this->docParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
    }

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if (!$node instanceof Property
            && !$node instanceof Variable
            && !$node instanceof ClassMethod
        ) {
            return;
        }

        $docComment = $node->getDocComment();
        if (!$docComment instanceof Doc) {
            return;
        }

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

        foreach ($docNode->getParamTagValues() as $tag) {
            $types = $this->typeResolver->resolvePHPStanDocParserType($tag->type, $typeScope, $templateTypes);

            foreach ($types as $type) {
                $referenceBuilder->parameter($type, $docComment->getStartLine());
            }
        }

        foreach ($docNode->getReturnTagValues() as $tag) {
            $types = $this->typeResolver->resolvePHPStanDocParserType($tag->type, $typeScope, $templateTypes);

            foreach ($types as $type) {
                $referenceBuilder->returnType($type, $docComment->getStartLine());
            }
        }

        foreach ($docNode->getThrowsTagValues() as $tag) {
            $types = $this->typeResolver->resolvePHPStanDocParserType($tag->type, $typeScope, $templateTypes);

            foreach ($types as $type) {
                $referenceBuilder->throwStatement($type, $docComment->getStartLine());
            }
        }
    }

    public function processNodeWithPhpStanScope(Node $node, ReferenceBuilder $referenceBuilder, Scope $scope): void
    {
        if (!$node instanceof Property
            && !$node instanceof Variable
            && !$node instanceof ClassMethod
        ) {
            return;
        }

        $docComment = $node->getDocComment();
        if (!$docComment instanceof Doc) {
            return;
        }

        $fileTypeMapper = $this->phpStanContainer->createFileTypeMapper();
        $function = $scope->getFunction();
        $resolvedPhpDoc = $fileTypeMapper->getResolvedPhpDoc(
            $scope->getFile(),
            $scope->isInClass() ? $scope->getClassReflection()->getName() : null,
            $scope->isInTrait() ? $scope->getTraitReflection()->getName() : null,
            $function !== null ? $function->getName() : null,
            $docComment->getText(),
        );


        foreach($resolvedPhpDoc->getVarTags() as $tag) {
            foreach($tag->getType()->getReferencedClasses() as $referencedClass) {
                $referenceBuilder->variable($referencedClass, $docComment->getStartLine());
            }
        }

        foreach($resolvedPhpDoc->getThrowsTag()?->getType()->getReferencedClasses() ?? [] as $referencedClass) {
            $referenceBuilder->throwStatement($referencedClass, $docComment->getStartLine());
        }

        $templates = $resolvedPhpDoc->getTemplateTags();
        if($templates === []) {
            foreach($resolvedPhpDoc->getParamTags() as $tag) {
                foreach($tag->getType()->getReferencedClasses() as $referencedClass) {
                    $referenceBuilder->parameter($referencedClass, $docComment->getStartLine());
                }
            }

            foreach($resolvedPhpDoc->getReturnTag()?->getType()->getReferencedClasses() ?? [] as $referencedClass) {
                $referenceBuilder->returnType($referencedClass, $docComment->getStartLine());
            }
        } else {
            assert($node instanceof ClassMethod);

            $methodVariant = $scope->getClassReflection()
                ->getMethod($node->name->name, $scope)
                ->getVariants()[0];

            foreach($methodVariant->getParameters() as $tag) {
                foreach($tag->getType()->getReferencedClasses() as $referencedClass) {
                    $referenceBuilder->parameter($referencedClass, $docComment->getStartLine());
                }
            }

            foreach($methodVariant->getPhpDocReturnType()->getReferencedClasses() as $referencedClass) {
                $referenceBuilder->returnType($referencedClass, $docComment->getStartLine());
            }
        }
    }

}
