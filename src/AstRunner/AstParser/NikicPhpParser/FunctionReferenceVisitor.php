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
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeVisitorAbstract;
use Qossmic\Deptrac\AstRunner\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\AstRunner\AstMap\FunctionToken\FunctionReferenceBuilder;
use Qossmic\Deptrac\AstRunner\Resolver\TypeResolver;
use Qossmic\Deptrac\AstRunner\Resolver\TypeScope;

class FunctionReferenceVisitor extends NodeVisitorAbstract
{
    private FileReferenceBuilder $fileReferenceBuilder;

    private TypeScope $currentTypeScope;

    private TypeResolver $typeResolver;

    private ?FunctionReferenceBuilder $currentFunctionReference;

    public function __construct(FileReferenceBuilder $fileReferenceBuilder, TypeResolver $typeResolver)
    {
        $this->currentTypeScope = new TypeScope('');
        $this->fileReferenceBuilder = $fileReferenceBuilder;
        $this->typeResolver = $typeResolver;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Namespace_) {
            $this->currentTypeScope = new TypeScope($node->name ? $node->name->toCodeString() : '');
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

        if (null === $this->currentFunctionReference) {
            return null;
        }

        if ($node instanceof Class_ && $node->isAnonymous()) {
            if ($node->extends instanceof Name) {
                $this->currentFunctionReference->extends($node->extends->toCodeString(), $node->extends->getLine());
            }
            foreach ($node->implements as $implement) {
                $this->currentFunctionReference->implements($implement->toCodeString(), $implement->getLine());
            }
        }

        if ($node instanceof TraitUse) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, ...$node->traits) as $classLikeName) {
                $this->currentFunctionReference->trait($classLikeName, $node->getLine());
            }
        }

        if ($node instanceof Attribute) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->name) as $classLikeName) {
                $this->currentFunctionReference->attribute($classLikeName, $node->getLine());
            }
        }

        if ($node instanceof Instanceof_ && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $this->currentFunctionReference->instanceof($classLikeName, $node->class->getLine());
            }
        }

        if ($node instanceof Param && null !== $node->type) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->type) as $classLikeName) {
                $this->currentFunctionReference->parameter($classLikeName, $node->type->getLine());
            }
        }

        if ($node instanceof New_ && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $this->currentFunctionReference->newStatement($classLikeName, $node->class->getLine());
            }
        }

        if ($node instanceof StaticPropertyFetch && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $this->currentFunctionReference->staticProperty($classLikeName, $node->class->getLine());
            }
        }

        if ($node instanceof StaticCall && $node->class instanceof Name) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->class) as $classLikeName) {
                $this->currentFunctionReference->staticMethod($classLikeName, $node->class->getLine());
            }
        }

        if (($node instanceof ClassMethod || $node instanceof Closure) && null !== $node->returnType) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, $node->returnType) as $classLikeName) {
                $this->currentFunctionReference->returnType($classLikeName, $node->returnType->getLine());
            }
        }

        if ($node instanceof Catch_) {
            foreach ($this->typeResolver->resolvePHPParserTypes($this->currentTypeScope, ...$node->types) as $classLikeName) {
                $this->currentFunctionReference->catchStmt($classLikeName, $node->getLine());
            }
        }
        //TODO: classResolvers (Patrick Kusebauch @ 08.07.21)
        return null;
    }
}
