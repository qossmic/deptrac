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
use Qossmic\Deptrac\AstRunner\AstMap\FileReferenceBuilder;
use Qossmic\Deptrac\AstRunner\Resolver\ClassDependencyResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeScope;

class ClassReferenceVisitor extends NodeVisitorAbstract
{
    private FileReferenceBuilder $fileReferenceBuilder;

    /** @var ClassDependencyResolver[] */
    private array $classDependencyResolvers;

    private TypeScope $currentTypeScope;

    private TypeResolver $typeResolver;
    private Lexer $lexer;
    private PhpDocParser $docParser;

    public function __construct(FileReferenceBuilder $fileReferenceBuilder, TypeResolver $typeResolver, ClassDependencyResolver ...$classDependencyResolvers)
    {
        $this->currentTypeScope = new TypeScope('');
        $this->lexer = new Lexer();
        $this->docParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
        $this->fileReferenceBuilder = $fileReferenceBuilder;
        $this->classDependencyResolvers = $classDependencyResolvers;
        $this->typeResolver = $typeResolver;
    }

    /**
     * @return string[]
     */
    private function classLikeTemplatesFromDocs(
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
        }

        if (!$node instanceof ClassLike) {
            return null;
        }

        if (isset($node->namespacedName)) {
            $className = $node->namespacedName->toCodeString();
        } elseif ($node->name instanceof Identifier) {
            $className = $node->name->toString();
        } else {
            return null; // map anonymous classes on current class
        }

        $classReferenceBuilder = $this->fileReferenceBuilder->newClassLike($className, $this->classLikeTemplatesFromDocs($node));

        if ($node instanceof Class_) {
            if ($node->extends instanceof Name) {
                $classReferenceBuilder->extends($node->extends->toCodeString(), $node->extends->getLine());
            }
            foreach ($node->implements as $implement) {
                $classReferenceBuilder->implements($implement->toCodeString(), $implement->getLine());
            }
        }

        if ($node instanceof Interface_) {
            foreach ($node->extends as $extend) {
                $classReferenceBuilder->implements($extend->toCodeString(), $extend->getLine());
            }
        }

        return null;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Use_ && Use_::TYPE_NORMAL === $node->type) {
            foreach ($node->uses as $use) {
                $this->currentTypeScope->addUse($use->name->toString(), $use->getAlias()->toString());
            }
        }

        if ($node instanceof GroupUse) {
            foreach ($node->uses as $use) {
                if (Use_::TYPE_NORMAL === $use->type) {
                    $classLikeName = $node->prefix->toString().'\\'.$use->name->toString();
                    $this->currentTypeScope->addUse($classLikeName, $use->getAlias()->toString());
                }
            }
        }

        $classReferenceBuilder = $this->fileReferenceBuilder->currentClassLike();

        if (null === $classReferenceBuilder) {
            return null;
        }

        if ($node instanceof TraitUse) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, ...$node->traits) as $classLikeName) {
                $classReferenceBuilder->trait($classLikeName, $node->getLine());
            }
        }

        if ($node instanceof Attribute) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->name) as $classLikeName) {
                $classReferenceBuilder->attribute($classLikeName, $node->getLine());
            }
        }

        if ($node instanceof Instanceof_ && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $classReferenceBuilder->instanceof($classLikeName, $node->class->getLine());
            }
        }

        if ($node instanceof Param && null !== $node->type) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->type) as $classLikeName) {
                $classReferenceBuilder->parameter($classLikeName, $node->type->getLine());
            }
        }

        if ($node instanceof New_ && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $classReferenceBuilder->newStatement($classLikeName, $node->class->getLine());
            }
        }

        if ($node instanceof StaticPropertyFetch && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $classReferenceBuilder->staticProperty($classLikeName, $node->class->getLine());
            }
        }

        if ($node instanceof StaticCall && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $classReferenceBuilder->staticMethod($classLikeName, $node->class->getLine());
            }
        }

        if (($node instanceof ClassMethod || $node instanceof Closure) && null !== $node->returnType) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->returnType) as $classLikeName) {
                $classReferenceBuilder->returnType($classLikeName, $node->returnType->getLine());
            }
        }

        if ($node instanceof Catch_) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, ...$node->types) as $classLikeName) {
                $classReferenceBuilder->catchStmt($classLikeName, $node->getLine());
            }
        }

        foreach ($this->classDependencyResolvers as $resolver) {
            $resolver->processNode($node, $classReferenceBuilder, $this->currentTypeScope);
        }

        return null;
    }

    public function afterTraverse(array $nodes)
    {
        return null;
    }
}
