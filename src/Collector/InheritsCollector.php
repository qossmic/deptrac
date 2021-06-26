<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use LogicException;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;

class InheritsCollector implements CollectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'inherits';
    }

    /**
     * {@inheritdoc}
     */
    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry
    ): bool {
        if (!$astTokenReference instanceof AstClassReference) {
            return false;
        }

        $classLikeName = $this->getClassLikeName($configuration);

        $tokenLikeName = $astTokenReference->getTokenLikeName();
        assert($tokenLikeName instanceof AstMap\ClassLikeName);
        foreach ($astMap->getClassInherits($tokenLikeName) as $inherit) {
            if ($inherit->getClassLikeName()->equals($classLikeName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function getClassLikeName(array $configuration): ClassLikeName
    {
        if (!isset($configuration['inherits']) || !is_string($configuration['inherits'])) {
            throw new LogicException('InheritsCollector needs the interface, trait or class name as a string.');
        }

        return ClassLikeName::fromFQCN($configuration['inherits']);
    }
}
