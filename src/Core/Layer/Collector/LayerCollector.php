<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use InvalidArgumentException;
use Qossmic\Deptrac\Contract\Ast\TokenReferenceInterface;
use Qossmic\Deptrac\Core\Layer\Exception\CircularReferenceException;
use Qossmic\Deptrac\Core\Layer\LayerResolverInterface;
use function array_key_exists;
use function is_string;
use function sprintf;
use function trigger_deprecation;

final class LayerCollector implements ConditionalCollectorInterface
{
    /**
     * @var array<string, array<string, bool|null>>
     */
    private array $resolved = [];

    public function __construct(private readonly LayerResolverInterface $resolver)
    {
    }

    public function satisfy(array $config, TokenReferenceInterface $reference): bool
    {
        if (isset($config['layer']) && !isset($config['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'LayerCollector should use the "value" key from this version');
            $config['value'] = $config['layer'];
        }

        if (!isset($config['value']) || !is_string($config['value'])) {
            throw new InvalidArgumentException('LayerCollector needs the layer configuration.');
        }
        $layer = $config['value'];

        if (!$this->resolver->has($layer)) {
            throw new InvalidArgumentException(sprintf('Unknown layer "%s" specified in collector.', $config['value']));
        }
        $token = $reference->getToken()->toString();

        if (array_key_exists($token, $this->resolved) && array_key_exists($layer, $this->resolved[$token])) {
            if (null === $this->resolved[$token][$layer]) {
                throw CircularReferenceException::circularTokenReference($token);
            }

            return $this->resolved[$token][$layer];
        }

        // Set resolved for current token to null in case resolver comes back to it (circular reference)
        $this->resolved[$token][$layer] = null;

        return $this->resolved[$token][$layer] = $this->resolver->isReferenceInLayer($config['value'], $reference);
    }

    public function resolvable(array $config): bool
    {
        /** @var array{layer?: string, value?: string} $config */
        if (isset($config['layer']) && !isset($config['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'LayerCollector should use the "value" key from this version');
            $config['value'] = $config['layer'];
        }

        /** @var array{layer?: string, value: string} $config */
        return $this->resolver->has($config['value']);
    }
}
