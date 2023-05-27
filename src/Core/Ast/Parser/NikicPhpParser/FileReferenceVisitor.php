<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeVisitorAbstract;
use PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ReferenceExtractorInterface;
use Qossmic\Deptrac\Core\Ast\Parser\TypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\TypeScope;

class FileReferenceVisitor extends NodeVisitorAbstract
{
    /** @var ReferenceExtractorInterface[] */
    private readonly array $dependencyResolvers;

    private TypeScope $currentTypeScope;
    private readonly Lexer $lexer;
    private readonly PhpDocParser $docParser;

    private ReferenceBuilder $currentReference;

    public function __construct(
        private readonly FileReferenceBuilder $fileReferenceBuilder,
        private readonly TypeResolver $typeResolver,
        ReferenceExtractorInterface ...$dependencyResolvers
    ) {
        $this->currentTypeScope = new TypeScope('');
        $this->lexer = new Lexer();
        $this->docParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
        $this->dependencyResolvers = $dependencyResolvers;
        $this->currentReference = $fileReferenceBuilder;
    }

    /**
     * @return string[]
     */
    private function templatesFromDocs(
        Node $node
    ): array {
        $docComment = $node->getDocComment();
        if (null === $docComment) {
            return [];
        }
        $docText = $docComment->getText();

        // prevent expensive parsing, when not templates involved.
        if (!str_contains($docText, '@template')) {
            return [];
        }

        $tokens = new TokenIterator($this->lexer->tokenize($docText));
        $docNode = $this->docParser->parse($tokens);

        return array_map(static fn (TemplateTagValueNode $tag): string => $tag->name, $docNode->getTemplateTagValues());
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Namespace_) {
            $this->currentTypeScope = new TypeScope($node->name ? $node->name->toCodeString() : '');

            return null;
        }

        if ($node instanceof Node\Stmt\Function_) {
            if (isset($node->namespacedName)) {
                $namespacedName = $node->namespacedName;
                $name = $namespacedName->toCodeString();
            } else {
                $name = $node->name->toString();
            }
            $this->enterFunction($name, $node);
            foreach ($node->getAttrGroups() as $attrGroup) {
                foreach ($attrGroup->attrs as $attribute) {
                    foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $attribute) as $classLikeName) {
                        $this->currentReference->attribute($classLikeName, $attribute->getLine());
                    }
                }
            }

            return null;
        }

        if ($node instanceof ClassLike) {
            $name = $this->getClassReferenceName($node);
            if (null !== $name) {
                $this->enterClassLike($name, $node);
            }
            foreach ($node->attrGroups as $attrGroup) {
                foreach ($attrGroup->attrs as $attribute) {
                    foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $attribute->name) as $classLikeName) {
                        $this->currentReference->attribute($classLikeName, $attribute->getLine());
                    }
                }
            }

            return null;
        }

        if ($node instanceof Node\FunctionLike) {
            foreach ($this->templatesFromDocs($node) as $template) {
                $this->currentReference->addTokenTemplate($template);
            }
        }

        return null;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\FunctionLike) {
            foreach ($this->templatesFromDocs($node) as $template) {
                $this->currentReference->removeTokenTemplate($template);
            }
        }

        // Resolve current reference scope
        if ($node instanceof Node\Stmt\Function_) {
            $this->currentReference = $this->fileReferenceBuilder;

            return null;
        }
        if ($node instanceof ClassLike && null !== $this->getClassReferenceName($node)) {
            $this->currentReference = $this->fileReferenceBuilder;

            return null;
        }

        // Resolve current type scope
        if ($node instanceof Use_ && Use_::TYPE_NORMAL === $node->type) {
            foreach ($node->uses as $use) {
                $this->currentTypeScope->addUse($use->name->toString(), $use->getAlias()->toString());
                $this->fileReferenceBuilder->useStatement($use->name->toString(), $use->name->getLine());
            }

            return null;
        }
        if ($node instanceof GroupUse) {
            foreach ($node->uses as $use) {
                if (Use_::TYPE_NORMAL === $use->type) {
                    $classLikeName = $node->prefix->toString().'\\'.$use->name->toString();
                    $this->currentTypeScope->addUse($classLikeName, $use->getAlias()->toString());
                    $this->fileReferenceBuilder->useStatement($classLikeName, $use->name->getLine());
                }
            }

            return null;
        }

        // Resolve code
        foreach ($this->dependencyResolvers as $resolver) {
            $resolver->processNode($node, $this->currentReference, $this->currentTypeScope);
        }

        return null;
    }

    private function enterClassLike(string $name, ClassLike $node): void
    {
        $isInternal = false;
        $docComment = $node->getDocComment();
        if (null !== $docComment) {
            $tokens = new TokenIterator($this->lexer->tokenize($docComment->getText()));
            $docNode = $this->docParser->parse($tokens);
            $isInternal = [] !== array_merge($docNode->getTagsByName('@internal'), $docNode->getTagsByName('@deptrac-internal'));
        }

        if ($node instanceof Interface_) {
            $this->currentReference = $this->fileReferenceBuilder->newInterface($name, $this->templatesFromDocs($node), $isInternal);

            foreach ($node->extends as $extend) {
                $this->currentReference->implements($extend->toCodeString(), $extend->getLine());
            }

            return;
        }

        if ($node instanceof Class_) {
            $this->currentReference = $this->fileReferenceBuilder->newClass($name, $this->templatesFromDocs($node), $isInternal);
            if ($node->extends instanceof Name) {
                $this->currentReference->extends($node->extends->toCodeString(), $node->extends->getLine());
            }

            foreach ($node->implements as $implement) {
                $this->currentReference->implements($implement->toCodeString(), $implement->getLine());
            }

            return;
        }

        if ($node instanceof Trait_) {
            $this->currentReference = $this->fileReferenceBuilder->newTrait($name, $this->templatesFromDocs($node), $isInternal);

            return;
        }

        $this->currentReference = $this->fileReferenceBuilder->newClassLike($name, $this->templatesFromDocs($node), $isInternal);
    }

    private function enterFunction(string $name, Node\Stmt\Function_ $node): void
    {
        $this->currentReference = $this->fileReferenceBuilder->newFunction($name, $this->templatesFromDocs($node));

        foreach ($node->getParams() as $param) {
            if (null !== $param->type) {
                foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $param->type) as $classLikeName) {
                    $this->currentReference->parameter($classLikeName, $param->getLine());
                }
            }
        }

        $returnType = $node->getReturnType();
        if (null !== $returnType) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $returnType) as $classLikeName) {
                $this->currentReference->returnType($classLikeName, $returnType->getLine());
            }
        }
    }

    private function getClassReferenceName(ClassLike $node): ?string
    {
        if (isset($node->namespacedName)) {
            return $node->namespacedName->toCodeString();
        }

        if ($node->name instanceof Identifier) {
            return $node->name->toString();
        }

        return null;
    }
}
