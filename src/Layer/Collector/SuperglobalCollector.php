<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Layer\Collector;

use LogicException;
use Qossmic\Deptrac\Ast\AstMap\AstMap;
use Qossmic\Deptrac\Ast\AstMap\TokenReferenceInterface;
use Qossmic\Deptrac\Ast\AstMap\Variable\VariableReference;

final class SuperglobalCollector implements CollectorInterface
{
    public function satisfy(array $config, TokenReferenceInterface $reference, AstMap $astMap): bool
    {
        if (!$reference instanceof VariableReference) {
            return false;
        }

        return in_array($reference->getToken()->toString(), $this->getNames($config), true);
    }

    /**
     * @param array<string, bool|string|array<string, string>> $config
     *
     * @return string[]
     */
    private function getNames(array $config): array
    {
        if (isset($config['names']) && !isset($config['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'SuperglobalCollector should use the "value" key from this version');
            $config['value'] = $config['names'];
        }

        if (!isset($config['value']) || !is_array($config['value'])) {
            throw new LogicException('SuperglobalCollector needs the names configuration.');
        }

        return array_map(static fn ($name): string => '$'.$name, $config['value']);
    }
}
