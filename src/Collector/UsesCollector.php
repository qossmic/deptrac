<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Collector;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;

class UsesCollector implements CollectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'uses';
    }

    /**
     * {@inheritdoc}
     */
    public function satisfy(
        array $configuration,
        AstClassReference $astClassReference,
        AstMap $astMap,
        Registry $collectorRegistry
    ): bool {
        $interfaceName = $this->getInterfaceName($configuration);

        foreach ($astMap->getClassInherits($astClassReference->getClassLikeName()) as $inherit) {
            if ($inherit->isUses() && $inherit->getClassLikeName()->equals($interfaceName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, string> $configuration
     */
    private function getInterfaceName(array $configuration): AstMap\ClassLikeName
    {
        if (!isset($configuration['uses'])) {
            throw new \LogicException('UsesCollector needs the trait name as a string.');
        }

        return AstMap\ClassLikeName::fromFQCN((string) $configuration['uses']);
    }
}
