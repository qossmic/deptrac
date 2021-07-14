<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeVisitorAbstract;
use PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Qossmic\Deptrac\AstRunner\AstMap\ClassToken\ClassReferenceBuilder;
use Qossmic\Deptrac\AstRunner\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\AstRunner\AstMap\FunctionToken\FunctionReferenceBuilder;
use Qossmic\Deptrac\AstRunner\Resolver\DependencyResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeScope;

class FileReferenceVisitor extends NodeVisitorAbstract
{
    private FileReferenceBuilder $fileReferenceBuilder;

    /** @var DependencyResolver[] */
    private array $dependencyResolvers;

    private TypeScope $currentTypeScope;

    private TypeResolver $typeResolver;
    private Lexer $lexer;
    private PhpDocParser $docParser;

    private ?ClassReferenceBuilder $currentClassReference;
    private ?FunctionReferenceBuilder $currentFunctionReference;

    public function __construct(FileReferenceBuilder $fileReferenceBuilder, TypeResolver $typeResolver, DependencyResolver ...$dependencyResolvers)
    {
        $this->currentTypeScope = new TypeScope('');
        $this->lexer = new Lexer();
        $this->docParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
        $this->fileReferenceBuilder = $fileReferenceBuilder;
        $this->dependencyResolvers = $dependencyResolvers;
        $this->typeResolver = $typeResolver;
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
        $tokens = new TokenIterator($this->lexer->tokenize($docComment->getText()));
        $docNode = $this->docParser->parse($tokens);

        return array_map(static function (TemplateTagValueNode $tag): string {
            return $tag->name;
        }, $docNode->getTemplateTagValues());
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Namespace_) {
            $this->currentTypeScope = new TypeScope($node->name ? $node->name->toCodeString() : '');
            return null;
        }

        if ($node instanceof ClassLike || $node instanceof Node\Stmt\Function_) {
            if (isset($node->namespacedName)) {
                $name = $node->namespacedName->toCodeString();
            } elseif ($node->name instanceof Identifier) {
                $name = $node->name->toString();
            } else {
                return null;
            }

            if ($node instanceof ClassLike) {
                $this->enterClassLike($name, $node);
                return null;
            }

            if ($node instanceof Node\Stmt\Function_) {
                $this->enterFunction($name, $node);
                return null;
            }

        }

        return null;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Use_ && Use_::TYPE_NORMAL === $node->type) {
            foreach ($node->uses as $use) {
                $this->currentTypeScope->addUse($use->name->toString(), $use->getAlias()->toString());
                $this->fileReferenceBuilder->newUseStatement($use->name->toString(), $use->name->getLine());
            }
        }

        if ($node instanceof GroupUse) {
            foreach ($node->uses as $use) {
                if (Use_::TYPE_NORMAL === $use->type) {
                    $classLikeName = $node->prefix->toString().'\\'.$use->name->toString();
                    $this->currentTypeScope->addUse($classLikeName, $use->getAlias()->toString());
                    $this->fileReferenceBuilder->newUseStatement($classLikeName, $use->name->getLine());
                }
            }
        }

        $currentTokenReference = $this->currentFunctionReference ?? $this->currentClassReference ?? null;

        if (null === $currentTokenReference) {
            return null;
        }

        if ($node instanceof TraitUse) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, ...$node->traits) as $classLikeName) {
                $this->currentClassReference->trait($classLikeName, $node->getLine());
            }
        }

        if ($node instanceof Attribute) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->name) as $classLikeName) {
                $this->currentClassReference->attribute($classLikeName, $node->getLine());
            }
        }

        if ($node instanceof Instanceof_ && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $this->currentClassReference->instanceof($classLikeName, $node->class->getLine());
            }
        }

        if ($node instanceof Param && null !== $node->type) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->type) as $classLikeName) {
                $this->currentClassReference->parameter($classLikeName, $node->type->getLine());
            }
        }

        if ($node instanceof New_ && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $this->currentClassReference->newStatement($classLikeName, $node->class->getLine());
            }
        }

        if ($node instanceof StaticPropertyFetch && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $this->currentClassReference->staticProperty($classLikeName, $node->class->getLine());
            }
        }

        if ($node instanceof StaticCall && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $this->currentClassReference->staticMethod($classLikeName, $node->class->getLine());
            }
        }

        if (($node instanceof ClassMethod || $node instanceof Closure) && null !== $node->returnType) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->returnType) as $classLikeName) {
                $this->currentClassReference->returnType($classLikeName, $node->returnType->getLine());
            }
        }

        if ($node instanceof Catch_) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, ...$node->types) as $classLikeName) {
                $this->currentClassReference->catchStmt($classLikeName, $node->getLine());
            }
        }

        foreach ($this->dependencyResolvers as $resolver) {
            $resolver->processNode($node, $this->currentClassReference, $this->currentTypeScope);
        }

        return null;
    }

    private function enterClassLike(string $name, ClassLike $node): void
    {
        $this->currentClassReference =
            $this->fileReferenceBuilder->newClassLike($name, $this->templatesFromDocs($node));

        if ($node instanceof Class_) {
            if ($node->extends instanceof Name) {
                $this->currentClassReference->extends($node->extends->toCodeString(), $node->extends->getLine());
            }
            foreach ($node->implements as $implement) {
                $this->currentClassReference->implements($implement->toCodeString(), $implement->getLine());
            }
        }

        if ($node instanceof Interface_) {
            foreach ($node->extends as $extend) {
                $this->currentClassReference->implements($extend->toCodeString(), $extend->getLine());
            }
        }
    }

    private function enterFunction(string $name, Node\Stmt\Function_ $node): void
    {
        $this->currentFunctionReference = $this->fileReferenceBuilder->newFunction($name);
        foreach ($node->getParams() as $param) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $param) as $classLikeName) {
                $this->currentFunctionReference->parameter($classLikeName, $node->getLine());
            }
        }

        foreach ($node->getAttrGroups() as $attrGroup) {
            foreach ($attrGroup->getAttributes() as $attribute) {
                //TODO: More resolving here (Patrick Kusebauch @ 10.07.21)
            }
        }

        foreach (
            $this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->getReturnType()) as
            $classLikeName
        ) {
            $this->currentFunctionReference->returnType($classLikeName, $node->getLine());
        }
    }
}
