<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Layer\Collector;

use Psr\Container\ContainerInterface;
use Qossmic\Deptrac\Contract\Layer\CollectorInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ServiceLocator;
use function array_keys;
use function sprintf;

final class CollectorProvider implements ContainerInterface
{
    public function __construct(private readonly ServiceLocator $collectorLocator)
    {
    }

    public function get(string $id): CollectorInterface
    {
        $collector = $this->collectorLocator->get($id);

        if (!$collector instanceof CollectorInterface) {
            $message = sprintf(
                'Type "%s" is not valid collector (expected "%s", but is "%s").',
                $id,
                CollectorInterface::class,
                get_debug_type($collector)
            );

            throw new RuntimeException($message);
        }

        return $collector;
    }

    public function has(string $id): bool
    {
        return $this->collectorLocator->has($id);
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     *
     * @return string[]
     */
    public function getKnownCollectors(): array
    {
        return array_keys($this->collectorLocator->getProvidedServices());
    }
}
