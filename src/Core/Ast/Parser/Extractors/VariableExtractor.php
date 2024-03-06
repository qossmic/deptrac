<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\Parser\Extractors;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Qossmic\Deptrac\Core\Ast\AstMap\ReferenceBuilder;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\SuperGlobalToken;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\NikicTypeResolver;
use Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\TypeScope;
use Qossmic\Deptrac\Core\Ast\Parser\PhpStanParser\PhpStanContainerDecorator;

/**
 * @implements ReferenceExtractorInterface<\PhpParser\Node\Expr\Variable>
 */
class VariableExtractor implements ReferenceExtractorInterface
{
    /**
     * @var list<string>
     */
    private array $allowedNames;
    private readonly Lexer $lexer;
    private readonly PhpDocParser $docParser;

    public function __construct(
        private readonly PhpStanContainerDecorator $phpStanContainer,
        private readonly NikicTypeResolver $typeResolver
    ) {
        $this->lexer = new Lexer();
        $this->docParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
        $this->allowedNames = SuperGlobalToken::allowedNames();
    }

    public function processNodeWithClassicScope(Node $node, ReferenceBuilder $referenceBuilder, TypeScope $typeScope): void
    {
        if (in_array($node->name, $this->allowedNames, true)) {
            $referenceBuilder->superglobal($node->name, $node->getLine());
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
        if (in_array($node->name, $this->allowedNames, true)) {
            $referenceBuilder->superglobal($node->name, $node->getLine());
        }

        $docComment = $node->getDocComment();
        if ($docComment instanceof Doc) {
            $fileTypeMapper = $this->phpStanContainer->createFileTypeMapper();
            $function = $scope->getFunction();
            $resolvedPhpDoc = $fileTypeMapper->getResolvedPhpDoc(
                $scope->getFile(),
                $scope->isInClass() ? $scope->getClassReflection()
                    ->getName() : null,
                $scope->isInTrait() ? $scope->getTraitReflection()
                    ->getName() : null,
                null !== $function ? $function->getName() : null,
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
        return Node\Expr\Variable::class;
    }
}
