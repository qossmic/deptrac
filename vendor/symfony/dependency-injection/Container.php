<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection;

use DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument\ServiceLocator as ArgumentServiceLocator;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\EnvNotFoundException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\ParameterCircularReferenceException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use DEPTRAC_202401\Symfony\Contracts\Service\ResetInterface;
// Help opcache.preload discover always-needed symbols
\class_exists(RewindableGenerator::class);
\class_exists(ArgumentServiceLocator::class);
/**
 * Container is a dependency injection container.
 *
 * It gives access to object instances (services).
 * Services and parameters are simple key/pair stores.
 * The container can have four possible behaviors when a service
 * does not exist (or is not initialized for the last case):
 *
 *  * EXCEPTION_ON_INVALID_REFERENCE: Throws an exception at compilation time (the default)
 *  * NULL_ON_INVALID_REFERENCE:      Returns null
 *  * IGNORE_ON_INVALID_REFERENCE:    Ignores the wrapping command asking for the reference
 *                                    (for instance, ignore a setter if the service does not exist)
 *  * IGNORE_ON_UNINITIALIZED_REFERENCE: Ignores/returns null for uninitialized services or invalid references
 *  * RUNTIME_EXCEPTION_ON_INVALID_REFERENCE: Throws an exception at runtime
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Container implements ContainerInterface, ResetInterface
{
    protected $parameterBag;
    protected $services = [];
    protected $privates = [];
    protected $fileMap = [];
    protected $methodMap = [];
    protected $factories = [];
    protected $aliases = [];
    protected $loading = [];
    protected $resolving = [];
    protected $syntheticIds = [];
    private array $envCache = [];
    private bool $compiled = \false;
    private \Closure $getEnv;
    private static \Closure $make;
    public function __construct(ParameterBagInterface $parameterBag = null)
    {
        $this->parameterBag = $parameterBag ?? new EnvPlaceholderParameterBag();
    }
    /**
     * Compiles the container.
     *
     * This method does two things:
     *
     *  * Parameter values are resolved;
     *  * The parameter bag is frozen.
     *
     * @return void
     */
    public function compile()
    {
        $this->parameterBag->resolve();
        $this->parameterBag = new FrozenParameterBag($this->parameterBag->all(), $this->parameterBag instanceof ParameterBag ? $this->parameterBag->allDeprecated() : []);
        $this->compiled = \true;
    }
    /**
     * Returns true if the container is compiled.
     */
    public function isCompiled() : bool
    {
        return $this->compiled;
    }
    /**
     * Gets the service container parameter bag.
     */
    public function getParameterBag() : ParameterBagInterface
    {
        return $this->parameterBag;
    }
    /**
     * Gets a parameter.
     *
     * @return array|bool|string|int|float|\UnitEnum|null
     *
     * @throws ParameterNotFoundException if the parameter is not defined
     */
    public function getParameter(string $name)
    {
        return $this->parameterBag->get($name);
    }
    public function hasParameter(string $name) : bool
    {
        return $this->parameterBag->has($name);
    }
    /**
     * @return void
     */
    public function setParameter(string $name, array|bool|string|int|float|\UnitEnum|null $value)
    {
        $this->parameterBag->set($name, $value);
    }
    /**
     * Sets a service.
     *
     * Setting a synthetic service to null resets it: has() returns false and get()
     * behaves in the same way as if the service was never created.
     *
     * @return void
     */
    public function set(string $id, ?object $service)
    {
        // Runs the internal initializer; used by the dumped container to include always-needed files
        if (isset($this->privates['service_container']) && $this->privates['service_container'] instanceof \Closure) {
            $initialize = $this->privates['service_container'];
            unset($this->privates['service_container']);
            $initialize($this);
        }
        if ('service_container' === $id) {
            throw new InvalidArgumentException('You cannot set service "service_container".');
        }
        if (!(isset($this->fileMap[$id]) || isset($this->methodMap[$id]))) {
            if (isset($this->syntheticIds[$id]) || !isset($this->getRemovedIds()[$id])) {
                // no-op
            } elseif (null === $service) {
                throw new InvalidArgumentException(\sprintf('The "%s" service is private, you cannot unset it.', $id));
            } else {
                throw new InvalidArgumentException(\sprintf('The "%s" service is private, you cannot replace it.', $id));
            }
        } elseif (isset($this->services[$id])) {
            throw new InvalidArgumentException(\sprintf('The "%s" service is already initialized, you cannot replace it.', $id));
        }
        if (isset($this->aliases[$id])) {
            unset($this->aliases[$id]);
        }
        if (null === $service) {
            unset($this->services[$id]);
            return;
        }
        $this->services[$id] = $service;
    }
    public function has(string $id) : bool
    {
        if (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }
        if (isset($this->services[$id])) {
            return \true;
        }
        if ('service_container' === $id) {
            return \true;
        }
        return isset($this->fileMap[$id]) || isset($this->methodMap[$id]);
    }
    /**
     * Gets a service.
     *
     * @throws ServiceCircularReferenceException When a circular reference is detected
     * @throws ServiceNotFoundException          When the service is not defined
     *
     * @see Reference
     */
    public function get(string $id, int $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE) : ?object
    {
        return $this->services[$id] ?? $this->services[$id = $this->aliases[$id] ?? $id] ?? ('service_container' === $id ? $this : ($this->factories[$id] ?? (self::$make ??= self::make(...)))($this, $id, $invalidBehavior));
    }
    /**
     * Creates a service.
     *
     * As a separate method to allow "get()" to use the really fast `??` operator.
     */
    private static function make(self $container, string $id, int $invalidBehavior) : ?object
    {
        if (isset($container->loading[$id])) {
            throw new ServiceCircularReferenceException($id, \array_merge(\array_keys($container->loading), [$id]));
        }
        $container->loading[$id] = \true;
        try {
            if (isset($container->fileMap[$id])) {
                return 4 === $invalidBehavior ? null : $container->load($container->fileMap[$id]);
            } elseif (isset($container->methodMap[$id])) {
                return 4 === $invalidBehavior ? null : $container->{$container->methodMap[$id]}($container);
            }
        } catch (\Exception $e) {
            unset($container->services[$id]);
            throw $e;
        } finally {
            unset($container->loading[$id]);
        }
        if (self::EXCEPTION_ON_INVALID_REFERENCE === $invalidBehavior) {
            if (!$id) {
                throw new ServiceNotFoundException($id);
            }
            if (isset($container->syntheticIds[$id])) {
                throw new ServiceNotFoundException($id, null, null, [], \sprintf('The "%s" service is synthetic, it needs to be set at boot time before it can be used.', $id));
            }
            if (isset($container->getRemovedIds()[$id])) {
                throw new ServiceNotFoundException($id, null, null, [], \sprintf('The "%s" service or alias has been removed or inlined when the container was compiled. You should either make it public, or stop using the container directly and use dependency injection instead.', $id));
            }
            $alternatives = [];
            foreach ($container->getServiceIds() as $knownId) {
                if ('' === $knownId || '.' === $knownId[0]) {
                    continue;
                }
                $lev = \levenshtein($id, $knownId);
                if ($lev <= \strlen($id) / 3 || \str_contains($knownId, $id)) {
                    $alternatives[] = $knownId;
                }
            }
            throw new ServiceNotFoundException($id, null, null, $alternatives);
        }
        return null;
    }
    /**
     * Returns true if the given service has actually been initialized.
     */
    public function initialized(string $id) : bool
    {
        if (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }
        if ('service_container' === $id) {
            return \false;
        }
        return isset($this->services[$id]);
    }
    /**
     * @return void
     */
    public function reset()
    {
        $services = $this->services + $this->privates;
        $this->services = $this->factories = $this->privates = [];
        foreach ($services as $service) {
            try {
                if ($service instanceof ResetInterface) {
                    $service->reset();
                }
            } catch (\Throwable) {
                continue;
            }
        }
    }
    /**
     * Gets all service ids.
     *
     * @return string[]
     */
    public function getServiceIds() : array
    {
        return \array_map('strval', \array_unique(\array_merge(['service_container'], \array_keys($this->fileMap), \array_keys($this->methodMap), \array_keys($this->aliases), \array_keys($this->services))));
    }
    /**
     * Gets service ids that existed at compile time.
     */
    public function getRemovedIds() : array
    {
        return [];
    }
    /**
     * Camelizes a string.
     */
    public static function camelize(string $id) : string
    {
        return \strtr(\ucwords(\strtr($id, ['_' => ' ', '.' => '_ ', '\\' => '_ '])), [' ' => '']);
    }
    /**
     * A string to underscore.
     */
    public static function underscore(string $id) : string
    {
        return \strtolower(\preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\\d])([A-Z])/'], ['\\1_\\2', '\\1_\\2'], \str_replace('_', '.', $id)));
    }
    /**
     * Creates a service by requiring its factory file.
     *
     * @return mixed
     */
    protected function load(string $file)
    {
        return require $file;
    }
    /**
     * Fetches a variable from the environment.
     *
     * @throws EnvNotFoundException When the environment variable is not found and has no default value
     */
    protected function getEnv(string $name) : mixed
    {
        if (isset($this->resolving[$envName = "env({$name})"])) {
            throw new ParameterCircularReferenceException(\array_keys($this->resolving));
        }
        if (isset($this->envCache[$name]) || \array_key_exists($name, $this->envCache)) {
            return $this->envCache[$name];
        }
        if (!$this->has($id = 'container.env_var_processors_locator')) {
            $this->set($id, new ServiceLocator([]));
        }
        $this->getEnv ??= $this->getEnv(...);
        $processors = $this->get($id);
        if (\false !== ($i = \strpos($name, ':'))) {
            $prefix = \substr($name, 0, $i);
            $localName = \substr($name, 1 + $i);
        } else {
            $prefix = 'string';
            $localName = $name;
        }
        $processor = $processors->has($prefix) ? $processors->get($prefix) : new EnvVarProcessor($this);
        if (\false === $i) {
            $prefix = '';
        }
        $this->resolving[$envName] = \true;
        try {
            return $this->envCache[$name] = $processor->getEnv($prefix, $localName, $this->getEnv);
        } finally {
            unset($this->resolving[$envName]);
        }
    }
    /**
     * @internal
     */
    protected final function getService(string|false $registry, string $id, ?string $method, string|bool $load) : mixed
    {
        if ('service_container' === $id) {
            return $this;
        }
        if (\is_string($load)) {
            throw new RuntimeException($load);
        }
        if (null === $method) {
            return \false !== $registry ? $this->{$registry}[$id] ?? null : null;
        }
        if (\false !== $registry) {
            return $this->{$registry}[$id] ??= $load ? $this->load($method) : $this->{$method}($this);
        }
        if (!$load) {
            return $this->{$method}($this);
        }
        return ($factory = $this->factories[$id] ?? $this->factories['service_container'][$id] ?? null) ? $factory($this) : $this->load($method);
    }
    private function __clone()
    {
    }
}
