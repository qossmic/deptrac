<?php

namespace SensioLabs\Deptrac\AstRunner;

use SensioLabs\Deptrac\AstRunner\AstMap\AstInheritInterface;
use SensioLabs\Deptrac\AstRunner\AstMap\FlattenAstInherit;
use SensioLabs\Deptrac\AstRunner\AstParser\AstClassReferenceInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstFileReferenceInterface;
use SensioLabs\Deptrac\AstRunner\AstParser\AstParserInterface;

class AstMap
{
    /**
     * @var AstClassReferenceInterface[]
     */
    private $astClassReferences = [];

    /**
     * @var AstFileReferenceInterface[]
     */
    private $astFileReferences = [];

    /**
     * @var AstParserInterface
     */
    private $astParser;

    public function __construct(AstParserInterface $astParser)
    {
        $this->astParser = $astParser;
    }

    public function addAstFileReference(AstFileReferenceInterface $astFileReference): void
    {
        $this->astFileReferences[$astFileReference->getFilepath()] = $astFileReference;

        foreach ($astFileReference->getAstClassReferences() as $astClassReference) {
            $this->addAstClassReference($astClassReference);
        }
    }

    /**
     * @return AstClassReferenceInterface[]
     */
    public function getAstClassReferences(): array
    {
        return $this->astClassReferences;
    }

    /**
     * @return AstFileReferenceInterface[]
     */
    public function getAstFileReferences(): array
    {
        return $this->astFileReferences;
    }

    public function getClassReferenceByClassName(string $className): ?AstClassReferenceInterface
    {
        return $this->astClassReferences[$className] ?? null;
    }

    /**
     * @return AstInheritInterface[]
     */
    public function getClassInherits(string $className): array
    {
        $buffer = [];

        foreach ($this->astParser->findInheritanceByClassname($className) as $dep) {
            $buffer[] = $dep;

            foreach ($this->resolveDepsRecursive($dep) as $recDep) {
                $buffer[] = $recDep;
            }
        }

        return $buffer;
    }

    /**
     * @return AstInheritInterface[]
     */
    private function resolveDepsRecursive(
        AstInheritInterface $inheritDependency,
        \ArrayObject $alreadyResolved = null,
        \SplStack $path = null
    ): array {
        if (null === $alreadyResolved) {
            $alreadyResolved = new \ArrayObject();
        }
        if (null === $path) {
            $path = new \SplStack();
            $path->push($inheritDependency);
        }

        if (isset($alreadyResolved[$inheritDependency->getClassName()])) {
            $path->pop();

            return [];
        }

        $buffer = [];
        foreach ($this->astParser->findInheritanceByClassname($inheritDependency->getClassName()) as $inherit) {
            $alreadyResolved[$inheritDependency->getClassName()] = true;

            $buffer[] = new FlattenAstInherit($inherit, iterator_to_array($path));
            $path->push($inherit);
            foreach ($this->resolveDepsRecursive($inherit, $alreadyResolved, $path) as $dep) {
                $buffer[] = $dep;
            }
            unset($alreadyResolved[$inheritDependency->getClassName()]);
            $path->pop();
        }

        return $buffer;
    }

    private function addAstClassReference(AstClassReferenceInterface $astClassReference): void
    {
        $this->astClassReferences[$astClassReference->getClassName()] = $astClassReference;
    }
}
