<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\AstRunner;

use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstFileReference;
use SensioLabs\Deptrac\AstRunner\AstMap\AstInherit;

class AstMap
{
    /**
     * @var AstClassReference[]
     */
    private $astClassReferences = [];

    /**
     * @var AstFileReference[]
     */
    private $astFileReferences = [];

    public function addAstFileReference(AstFileReference $astFileReference): void
    {
        $this->astFileReferences[$astFileReference->getFilepath()] = $astFileReference;

        foreach ($astFileReference->getAstClassReferences() as $astClassReference) {
            $this->addAstClassReference($astClassReference);
        }
    }

    /**
     * @return AstClassReference[]
     */
    public function getAstClassReferences(): array
    {
        return $this->astClassReferences;
    }

    /**
     * @return AstFileReference[]
     */
    public function getAstFileReferences(): array
    {
        return $this->astFileReferences;
    }

    public function getClassReferenceByClassName(string $className): ?AstClassReference
    {
        return $this->astClassReferences[$className] ?? null;
    }

    /**
     * @return AstInherit[]|iterable
     */
    public function getClassInherits(string $className): iterable
    {
        $classReference = $this->getClassReferenceByClassName($className);

        if (null === $classReference) {
            return [];
        }

        foreach ($classReference->getInherits() as $dep) {
            yield $dep;
            yield from $this->resolveDepsRecursive($dep);
        }
    }

    /**
     * @return AstInherit[]|iterable
     */
    private function resolveDepsRecursive(
        AstInherit $inheritDependency,
        \ArrayObject $alreadyResolved = null,
        \SplStack $path = null
    ): iterable {
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

        $classReference = $this->getClassReferenceByClassName($inheritDependency->getClassName());

        if (null === $classReference) {
            return [];
        }

        foreach ($classReference->getInherits() as $inherit) {
            $alreadyResolved[$inheritDependency->getClassName()] = true;

            yield $inherit->withPath(iterator_to_array($path));

            $path->push($inherit);

            yield from $this->resolveDepsRecursive($inherit, $alreadyResolved, $path);

            unset($alreadyResolved[$inheritDependency->getClassName()]);
            $path->pop();
        }
    }

    private function addAstClassReference(AstClassReference $astClassReference): void
    {
        $this->astClassReferences[$astClassReference->getClassName()] = $astClassReference;
    }
}
