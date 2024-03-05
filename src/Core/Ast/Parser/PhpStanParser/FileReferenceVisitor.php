<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Reflection\ReflectionProvider;
use Qossmic\Deptrac\Core\Ast\AstMap\File\FileReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\Parser\Extractors\ReferenceExtractorInterface;

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
        foreach ($this->dependencyResolvers as $resolver) {
            if ($node instanceof ($resolver->getNodeType())) {
                $resolver->processNodeWithPhpStanScope($node, $this->currentReference, $this->scope);
            }
        }

        $this->currentReference = match (true) {
            $node instanceof Node\Stmt\Function_ => $this->fileReferenceBuilder,
            $node instanceof ClassLike && null !== $this->getReferenceName($node) => $this->fileReferenceBuilder,
            default => $this->currentReference
        };

        return null;
    }

    private function enterClassLike(ClassLike $node): void
    {
        $name = $this->getReferenceName($node);
        $context = ScopeContext::create($this->file)->enterClass($this->reflectionProvider->getClass($name));
        $this->scope = $this->scopeFactory->create($context);

        if (null !== $name) {
            $this->currentReference = match (true) {
                $node instanceof Interface_ => $this->fileReferenceBuilder->newInterface($name, [], []),
                $node instanceof Class_ => $this->fileReferenceBuilder->newClass($name, [], []),
                $node instanceof Trait_ => $this->fileReferenceBuilder->newTrait($name, [], []),
                default => $this->fileReferenceBuilder->newClassLike($name, [], [])
            };
        }
    }

    private function enterFunction(Node\Stmt\Function_ $node): void
    {
        $name = $this->getReferenceName($node);
        assert(null !== $name);

        $this->currentReference = $this->fileReferenceBuilder->newFunction($name);
    }

    private function getReferenceName(Node\Stmt\Function_|ClassLike $node): ?string
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
