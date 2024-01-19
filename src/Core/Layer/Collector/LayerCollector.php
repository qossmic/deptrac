<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Layer\Collector;

use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;
use Qossmic\Deptrac\Contract\Layer\InvalidLayerDefinitionException;
use Qossmic\Deptrac\Core\Layer\LayerResolverInterface;
use function array_key_exists;
use function is_string;
use function sprintf;
final class LayerCollector implements \Qossmic\Deptrac\Core\Layer\Collector\ConditionalCollectorInterface
{
    /**
     * @var array<string, array<string, bool|null>>
     */
    private array $resolved = [];
    public function __construct(private readonly LayerResolverInterface $resolver)
    {
    }
    public function satisfy(array $config, TokenReferenceInterface $reference) : bool
    {
        if (!isset($config['value']) || !is_string($config['value'])) {
            throw InvalidCollectorDefinitionException::invalidCollectorConfiguration('LayerCollector needs the layer configuration.');
        }
        $layer = $config['value'];
        if (!$this->resolver->has($layer)) {
            throw InvalidCollectorDefinitionException::invalidCollectorConfiguration(sprintf('Unknown layer "%s" specified in collector.', $config['value']));
        }
        $token = $reference->getToken()->toString();
        if (array_key_exists($token, $this->resolved) && array_key_exists($layer, $this->resolved[$token])) {
            if (null === $this->resolved[$token][$layer]) {
                throw InvalidLayerDefinitionException::circularTokenReference($token);
            }
            return $this->resolved[$token][$layer];
        }
        // Set resolved for current token to null in case resolver comes back to it (circular reference)
        $this->resolved[$token][$layer] = null;
        return $this->resolved[$token][$layer] = $this->resolver->isReferenceInLayer($config['value'], $reference);
    }
    public function resolvable(array $config) : bool
    {
        /** @var array{layer?: string, value: string} $config */
        return $this->resolver->has($config['value']);
    }
}
