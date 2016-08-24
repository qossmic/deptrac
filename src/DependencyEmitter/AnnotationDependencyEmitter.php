<?php

namespace SensioLabs\Deptrac\DependencyEmitter;

use PhpParser\Comment\Doc;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use SensioLabs\AstRunner\AstParser\AstFileReferenceInterface;
use SensioLabs\Deptrac\DependencyEmitter\AnnotationDependencyEmitter\DocBlockVisitor;
use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\Deptrac\DependencyResult\Dependency;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;

class AnnotationDependencyEmitter implements DependencyEmitterInterface
{
    private $supportedAnnotations = ['var', 'param', 'return', 'throws'];
    
    public function getName()
    {
        return 'AnnotationDependencyEmitter';
    }

    public function supportsParser(AstParserInterface $astParser)
    {
        return $astParser instanceof NikicPhpParser;
    }

    private function getStatements(NikicPhpParser $astParser, AstClassReferenceInterface $classReference, AstFileReferenceInterface $fileReference)
    {
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());
        $docBlocks = self::getDocBlocks($ast);

        foreach ($docBlocks as $property) {
            /** @var $property Doc */
            $docComment = $property->getText();

            if (empty($docComment)) {
                continue;
            }

            foreach ($this->supportedAnnotations as $annotation) {
                $buffer = array_merge($buffer, $this->parseAnnotation(
                    $astParser,
                    $fileReference,
                    $annotation,
                    $docComment,
                    $property->getLine()
                ));
            }
        }

        return $buffer;
    }

    private function parseAnnotation(NikicPhpParser $astParser, AstFileReferenceInterface $fileReference, $annotation, $docComment, $line)
    {
        preg_match_all('/\* @' . $annotation . ' (.*)\\s/imsU', $docComment, $matches);
        $classNames = isset($matches[1]) ? $matches[1] : null;

        if (empty($classNames)) {
            return [];
        }

        $buffer = [];

        foreach ($classNames as $className) {
            // @TODO maybe we could skip here values which are obvious no class
            // names like string, integer etc.

            // resolve the class name based on the defined uses if needed
            $className = $this->resolveClassName($astParser, $fileReference, $className);
            if (empty($className)) {
                continue;
            }

            // check whether this class exists
            $ast = $astParser->getAstForClassname($className);
            if ($ast instanceof Class_) {
                $buffer[] = new EmittedDependency(
                    $className,
                    $line,
                    $annotation
                );
            }
        }

        return $buffer;
    }

    private function resolveClassName(NikicPhpParser $astParser, AstFileReferenceInterface $fileReference, $className)
    {
        // we have an fqcn
        if (isset($className[0]) && $className[0] == '\\') {
            return substr($className, 1);
        }

        // resolve class name based on defined uses
        if (strpos($className, '\\') !== false) {
            $alias = strstr($className, '\\', true);
        } else {
            $alias = $className;
        }

        $namespace = null;
        foreach ($astParser->getAstByFile($fileReference) as $namespaceNode) {
            if (!$namespaceNode instanceof Namespace_ || !$namespaceNode->stmts) {
                continue; // @codeCoverageIgnore
            }

            $namespace = $namespaceNode->name->toString();
            
            foreach ($namespaceNode->stmts as $useNodes) {
                if (!$useNodes instanceof Use_) {
                    continue; // @codeCoverageIgnore
                }

                foreach ($useNodes->uses as $useNode) {
                    if ($useNode->alias == $alias) {
                        return $useNode->name->toString();
                    }
                }
            }
        }

        if (!empty($namespace)) {
            return Name::concat($namespace, $className)->toString();
        }

        return null;
    }

    private static function getDocBlocks($ast)
    {
        $visitor = new DocBlockVisitor();
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse([$ast]);

        return $visitor->getDocBlocks();
    }

    public function applyDependencies(
        AstParserInterface $astParser,
        AstMap $astMap,
        DependencyResult $dependencyResult
    )
    {
        /* @var $astParser NikicPhpParser */
        assert($astParser instanceof NikicPhpParser);

        foreach ($astMap->getAstFileReferences() as $fileReference) {
            foreach ($fileReference->getAstClassReferences() as $astClassReference) {

                /** @var $dependencies EmittedDependency[] */
                $dependencies = $this->getStatements($astParser, $astClassReference, $fileReference);

                foreach ($dependencies as $emittedDependency) {
                    $dependencyResult->addDependency(
                        new Dependency(
                            $astClassReference->getClassName(), $emittedDependency->getLine(), $emittedDependency->getClass()
                        )
                    );
                }
            }
        }
    }
}
