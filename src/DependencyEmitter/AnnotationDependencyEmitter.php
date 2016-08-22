<?php

namespace SensioLabs\Deptrac\DependencyEmitter;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use SensioLabs\AstRunner\AstParser\AstFileReferenceInterface;
use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\Deptrac\DependencyResult\Dependency;
use PhpParser\Node\Stmt\Property;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;

class AnnotationDependencyEmitter implements DependencyEmitterInterface
{
    public function getName()
    {
        return 'AnnotationDependencyEmitter';
    }

    public function supportsParser(AstParserInterface $astParser)
    {
        return $astParser instanceof NikicPhpParser;
    }

    private function getPropertyStatements(NikicPhpParser $astParser, AstClassReferenceInterface $classReference, AstFileReferenceInterface $fileReference)
    {
        $annotations = ['var'];
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());
        foreach ($astParser->findNodesOfType($ast, Property::class) as $property) {
            /** @var $property Property */
            $docComment = $property->getDocComment();

            if (empty($docComment)) {
                continue;
            }

            foreach ($annotations as $annotation) {
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

    private function getMethodStatements(NikicPhpParser $astParser, AstClassReferenceInterface $classReference, AstFileReferenceInterface $fileReference)
    {
        $annotations = ['param', 'return', 'throws'];
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());
        foreach ($astParser->findNodesOfType($ast, ClassMethod::class) as $method) {
            /** @var $method ClassMethod */
            $docComment = $method->getDocComment();

            if (empty($docComment)) {
                continue; // @codeCoverageIgnore
            }

            foreach ($annotations as $annotation) {
                $buffer = array_merge($buffer, $this->parseAnnotation(
                    $astParser,
                    $fileReference,
                    $annotation,
                    $docComment,
                    $method->getLine()
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
                $dependencies = array_merge(
                    $this->getPropertyStatements($astParser, $astClassReference, $fileReference), 
                    $this->getMethodStatements($astParser, $astClassReference, $fileReference)
                );

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
