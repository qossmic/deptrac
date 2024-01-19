<?php

declare (strict_types=1);
namespace Qossmic\Deptrac\Core\Layer\Collector;

use DEPTRAC_202401\Psr\Container\ContainerInterface;
use Qossmic\Deptrac\Contract\Layer\CollectorInterface;
use Qossmic\Deptrac\Contract\Layer\InvalidCollectorDefinitionException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\ServiceLocator;
use function array_keys;
final class CollectorProvider implements ContainerInterface
{
    /**
     * @param ServiceLocator<mixed> $collectorLocator
     */
    public function __construct(private readonly ServiceLocator $collectorLocator)
    {
    }
    public function get(string $id) : CollectorInterface
    {
        $collector = $this->collectorLocator->get($id);
        if (!$collector instanceof CollectorInterface) {
            $exception = InvalidCollectorDefinitionException::unsupportedClass($id, $collector);
            throw new \DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\RuntimeException($exception->getMessage(), 0, $exception);
        }
        return $collector;
    }
    public function has(string $id) : bool
    {
        return $this->collectorLocator->has($id);
    }
    /**
     * @psalm-suppress MixedReturnTypeCoercion
     *
     * @return string[]
     */
    public function getKnownCollectors() : array
    {
        return array_keys($this->collectorLocator->getProvidedServices());
    }
}
