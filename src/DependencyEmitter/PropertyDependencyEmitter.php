<?php

namespace SensioLabs\Deptrac\DependencyEmitter;

use PhpParser\Node\Stmt\Class_;
use SensioLabs\Deptrac\DependencyResult;
use SensioLabs\Deptrac\DependencyResult\Dependency;
use PhpParser\Node\Stmt\Property;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\AstRunner\AstParser\AstParserInterface;
use SensioLabs\AstRunner\AstParser\NikicPhpParser\NikicPhpParser;

class PropertyDependencyEmitter implements DependencyEmitterInterface
{
    public function getName()
    {
        return 'PropertyDependencyEmitter';
    }

    public function supportsParser(AstParserInterface $astParser)
    {
        return $astParser instanceof NikicPhpParser;
    }

    private function getPropertyStatements(NikicPhpParser $astParser, AstClassReferenceInterface $classReference)
    {
        $buffer = [];
        $ast = $astParser->getAstForClassname($classReference->getClassName());
        foreach ($astParser->findNodesOfType($ast, Property::class) as $property) {
            /** @var $property Property */
            $docComment = $property->getDocComment();

            if (!empty($docComment)) {
                preg_match('/\* @var (.*)\\s/imsU', $docComment, $matches);
                $className = isset($matches[1]) ? ltrim($matches[1], '\\') : null;

                if (!empty($className)) {
                    // check whether this class exists
                    $ast = $astParser->getAstForClassname($className);
                    if ($ast instanceof Class_) {
                        $buffer[] = new EmittedDependency(
                            $className,
                            $property->getLine(),
                            'property'
                        );
                    }
                }
            }
        }

        return $buffer;
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
                $dependencies = $this->getPropertyStatements($astParser, $astClassReference);

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
