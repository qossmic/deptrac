<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Contract\Layer\CollectorInterface;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\VariableReference;
use Qossmic\Deptrac\Core\Layer\Exception\InvalidLayerDefinitionException;

final class SuperglobalCollector implements CollectorInterface
{
    public function satisfy(array $config, TokenReferenceInterface $reference): bool
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
     *
     * @throws InvalidLayerDefinitionException
     */
    private function getNames(array $config): array
    {
        if (isset($config['names']) && !isset($config['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'SuperglobalCollector should use the "value" key from this version');
            $config['value'] = $config['names'];
        }

        if (!isset($config['value']) || !is_array($config['value'])) {
            throw InvalidLayerDefinitionException::invalidCollectorConfiguration('SuperglobalCollector needs the names configuration.');
        }

        return array_map(static fn ($name): string => '$'.$name, $config['value']);
    }
}
