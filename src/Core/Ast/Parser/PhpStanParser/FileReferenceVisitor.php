<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser;

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
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\Reflection\ReflectionProvider;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ReferenceExtractorInterface;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;

class FileReferenceVisitor extends NodeVisitorAbstract
{
    /** @var ReferenceExtractorInterface[] */
    private readonly array $dependencyResolvers;

    private ReferenceBuilder $currentReference;

    private Scope $scope;

    public function __construct(
        private readonly FileReferenceBuilder $fileReferenceBuilder,
        private readonly ScopeFactory $scopeFactory,
        private readonly ReflectionProvider $reflectionProvider,
        private readonly string $file,
        ReferenceExtractorInterface ...$dependencyResolvers
    ) {
        $this->dependencyResolvers = $dependencyResolvers;
        $this->currentReference = $fileReferenceBuilder;
        $this->scope = $this->scopeFactory->create(ScopeContext::create($this->file));
    }

    public function enterNode(Node $node)
    {
        match (true) {
            $node instanceof Node\Stmt\Function_ => $this->enterFunction($node),
            $node instanceof ClassLike => $this->enterClassLike($node),
            default => null
        };

        return null;
    }

    public function leaveNode(Node $node)
    {
        match (true) {
            $node instanceof Node\Stmt\Function_ => $this->currentReference = $this->fileReferenceBuilder,
            $node instanceof ClassLike && null !== $this->getClassReferenceName($node) => $this->currentReference = $this->fileReferenceBuilder,
            $node instanceof Use_ && Use_::TYPE_NORMAL === $node->type => $this->leaveUse($node),
            $node instanceof GroupUse => $this->leaveGroupUse($node),
            default => null
        };

        foreach ($this->dependencyResolvers as $resolver) {
            $resolver->processNodeWithPhpStanScope($node, $this->currentReference, $this->scope);
        }

        return null;
    }

    private function enterClassLike(ClassLike $node): void
    {
        $name = $this->getClassReferenceName($node);
        $context = ScopeContext::create($this->file)->enterClass($this->reflectionProvider->getClass($name));
        $this->scope = $this->scopeFactory->create($context);
        if (null !== $name) {
            match (true) {
                $node instanceof Interface_ => $this->enterInterface($name, $node, []),
                $node instanceof Class_ => $this->enterClass($name, $node, []),
                $node instanceof Trait_ => $this->currentReference =
                    $this->fileReferenceBuilder->newTrait($name, [], []),
                default => $this->currentReference =
                    $this->fileReferenceBuilder->newClassLike($name, [], [])
            };
        }


        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                $this->currentReference->attribute($this->scope->resolveName($attribute->name), $attribute->getLine());
            }
        }
    }

    private function enterFunction(Node\Stmt\Function_ $node): void
    {
        if (isset($node->namespacedName)) {
            $namespacedName = $node->namespacedName;
            $name = $namespacedName->toCodeString();
        } else {
            $name = $node->name->toString();
        }

        $this->currentReference = $this->fileReferenceBuilder->newFunction($name, [], []);

        foreach ($node->getParams() as $param) {
            if (null !== $param->type) {
                $this->currentReference->parameter($this->scope->resolveName($param->name), $param->getLine());
            }
        }

        $returnType = $node->getReturnType();
        if (null !== $returnType) {
            $this->currentReference->returnType($this->scope->resolveName($returnType), $returnType->getLine());
        }

        foreach ($node->getAttrGroups() as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                $this->currentReference->attribute($this->scope->resolveName($attribute->name), $attribute->getLine());
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

    /**
     * @param array<string,list<string>> $tags
     */
    private function enterInterface(string $name, Interface_ $node, array $tags): void
    {
        $this->currentReference = $this->fileReferenceBuilder->newInterface($name, [], $tags);

        foreach ($node->extends as $extend) {
            $this->currentReference->implements($extend->toCodeString(), $extend->getLine());
        }
    }

    /**
     * @param array<string,list<string>> $tags
     */
    private function enterClass(string $name, Class_ $node, array $tags): void
    {
        $this->currentReference = $this->fileReferenceBuilder->newClass($name, [], $tags);
        if ($node->extends instanceof Name) {
            $this->currentReference->extends($node->extends->toCodeString(), $node->extends->getLine());
        }

        foreach ($node->implements as $implement) {
            $this->currentReference->implements($implement->toCodeString(), $implement->getLine());
        }
    }

    private function leaveUse(Use_ $node): void
    {
        foreach ($node->uses as $use) {
            $this->fileReferenceBuilder->useStatement($use->name->toString(), $use->name->getLine());
        }
    }

    private function leaveGroupUse(GroupUse $node): void
    {
        foreach ($node->uses as $use) {
            if (Use_::TYPE_NORMAL === $use->type) {
                $classLikeName = $node->prefix->toString().'\\'.$use->name->toString();
                $this->fileReferenceBuilder->useStatement($classLikeName, $use->name->getLine());
            }
        }
    }

}
