<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Contract\Layer\CollectorInterface;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;
use Qossmic\Deptrac\Core\Ast\AstMap\Variable\VariableReference;
final class SuperglobalCollector implements CollectorInterface
{
    public function satisfy(array $config, TokenReferenceInterface $reference) : bool
    {
        if (!$reference instanceof VariableReference) {
            return \false;
        }
        return \in_array($reference->getToken()->toString(), $this->getNames($config), \true);
    }
    /**
     * @param array<string, bool|string|array<string, string>> $config
     *
     * @return string[]
     *
     * @throws InvalidCollectorDefinitionException
     */
    private function getNames(array $config) : array
    {
        if (!isset($config['value']) || !\is_array($config['value'])) {
            throw InvalidCollectorDefinitionException::invalidCollectorConfiguration('SuperglobalCollector needs the names configuration.');
        }
        return \array_map(static fn($name): string => '$' . $name, $config['value']);
    }
}
