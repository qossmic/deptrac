<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\ParameterBag;

use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\ParameterCircularReferenceException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\RuntimeException;
/**
 * Holds parameters.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ParameterBag implements ParameterBagInterface
{
    protected $parameters = [];
    protected $resolved = \false;
    protected array $deprecatedParameters = [];
    public function __construct(array $parameters = [])
    {
        $this->add($parameters);
    }
    /**
     * @return void
     */
    public function clear()
    {
        $this->parameters = [];
    }
    /**
     * @return void
     */
    public function add(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $this->set($key, $value);
        }
    }
    public function all() : array
    {
        return $this->parameters;
    }
    public function allDeprecated() : array
    {
        return $this->deprecatedParameters;
    }
    public function get(string $name) : array|bool|string|int|float|\UnitEnum|null
    {
        if (!\array_key_exists($name, $this->parameters)) {
            if (!$name) {
                throw new ParameterNotFoundException($name);
            }
            $alternatives = [];
            foreach ($this->parameters as $key => $parameterValue) {
                $lev = \levenshtein($name, $key);
                if ($lev <= \strlen($name) / 3 || \str_contains($key, $name)) {
                    $alternatives[] = $key;
                }
            }
            $nonNestedAlternative = null;
            if (!\count($alternatives) && \str_contains($name, '.')) {
                $namePartsLength = \array_map('strlen', \explode('.', $name));
                $key = \substr($name, 0, -1 * (1 + \array_pop($namePartsLength)));
                while (\count($namePartsLength)) {
                    if ($this->has($key)) {
                        if (\is_array($this->get($key))) {
                            $nonNestedAlternative = $key;
                        }
                        break;
                    }
                    $key = \substr($key, 0, -1 * (1 + \array_pop($namePartsLength)));
                }
            }
            throw new ParameterNotFoundException($name, null, null, null, $alternatives, $nonNestedAlternative);
        }
        if (isset($this->deprecatedParameters[$name])) {
            \DEPTRAC_202401\trigger_deprecation(...$this->deprecatedParameters[$name]);
        }
        return $this->parameters[$name];
    }
    /**
     * @return void
     */
    public function set(string $name, array|bool|string|int|float|\UnitEnum|null $value)
    {
        if (\is_numeric($name)) {
            \DEPTRAC_202401\trigger_deprecation('symfony/dependency-injection', '6.2', \sprintf('Using numeric parameter name "%s" is deprecated and will throw as of 7.0.', $name));
            // uncomment the following line in 7.0
            // throw new InvalidArgumentException(sprintf('The parameter name "%s" cannot be numeric.', $name));
        }
        $this->parameters[$name] = $value;
    }
    /**
     * Deprecates a service container parameter.
     *
     * @return void
     *
     * @throws ParameterNotFoundException if the parameter is not defined
     */
    public function deprecate(string $name, string $package, string $version, string $message = 'The parameter "%s" is deprecated.')
    {
        if (!\array_key_exists($name, $this->parameters)) {
            throw new ParameterNotFoundException($name);
        }
        $this->deprecatedParameters[$name] = [$package, $version, $message, $name];
    }
    public function has(string $name) : bool
    {
        return \array_key_exists($name, $this->parameters);
    }
    /**
     * @return void
     */
    public function remove(string $name)
    {
        unset($this->parameters[$name], $this->deprecatedParameters[$name]);
    }
    /**
     * @return void
     */
    public function resolve()
    {
        if ($this->resolved) {
            return;
        }
        $parameters = [];
        foreach ($this->parameters as $key => $value) {
            try {
                $value = $this->resolveValue($value);
                $parameters[$key] = $this->unescapeValue($value);
            } catch (ParameterNotFoundException $e) {
                $e->setSourceKey($key);
                throw $e;
            }
        }
        $this->parameters = $parameters;
        $this->resolved = \true;
    }
    /**
     * Replaces parameter placeholders (%name%) by their values.
     *
     * @template TValue of array<array|scalar>|scalar
     *
     * @param TValue $value
     * @param array  $resolving An array of keys that are being resolved (used internally to detect circular references)
     *
     * @psalm-return (TValue is scalar ? array|scalar : array<array|scalar>)
     *
     * @throws ParameterNotFoundException          if a placeholder references a parameter that does not exist
     * @throws ParameterCircularReferenceException if a circular reference if detected
     * @throws RuntimeException                    when a given parameter has a type problem
     */
    public function resolveValue(mixed $value, array $resolving = []) : mixed
    {
        if (\is_array($value)) {
            $args = [];
            foreach ($value as $key => $v) {
                $resolvedKey = \is_string($key) ? $this->resolveValue($key, $resolving) : $key;
                if (!\is_scalar($resolvedKey) && !$resolvedKey instanceof \Stringable) {
                    throw new RuntimeException(\sprintf('Array keys must be a scalar-value, but found key "%s" to resolve to type "%s".', $key, \get_debug_type($resolvedKey)));
                }
                $args[$resolvedKey] = $this->resolveValue($v, $resolving);
            }
            return $args;
        }
        if (!\is_string($value) || '' === $value || !\str_contains($value, '%')) {
            return $value;
        }
        return $this->resolveString($value, $resolving);
    }
    /**
     * Resolves parameters inside a string.
     *
     * @param array $resolving An array of keys that are being resolved (used internally to detect circular references)
     *
     * @throws ParameterNotFoundException          if a placeholder references a parameter that does not exist
     * @throws ParameterCircularReferenceException if a circular reference if detected
     * @throws RuntimeException                    when a given parameter has a type problem
     */
    public function resolveString(string $value, array $resolving = []) : mixed
    {
        // we do this to deal with non string values (Boolean, integer, ...)
        // as the preg_replace_callback throw an exception when trying
        // a non-string in a parameter value
        if (\preg_match('/^%([^%\\s]+)%$/', $value, $match)) {
            $key = $match[1];
            if (isset($resolving[$key])) {
                throw new ParameterCircularReferenceException(\array_keys($resolving));
            }
            $resolving[$key] = \true;
            return $this->resolved ? $this->get($key) : $this->resolveValue($this->get($key), $resolving);
        }
        return \preg_replace_callback('/%%|%([^%\\s]+)%/', function ($match) use($resolving, $value) {
            // skip %%
            if (!isset($match[1])) {
                return '%%';
            }
            $key = $match[1];
            if (isset($resolving[$key])) {
                throw new ParameterCircularReferenceException(\array_keys($resolving));
            }
            $resolved = $this->get($key);
            if (!\is_string($resolved) && !\is_numeric($resolved)) {
                throw new RuntimeException(\sprintf('A string value must be composed of strings and/or numbers, but found parameter "%s" of type "%s" inside string value "%s".', $key, \get_debug_type($resolved), $value));
            }
            $resolved = (string) $resolved;
            $resolving[$key] = \true;
            return $this->isResolved() ? $resolved : $this->resolveString($resolved, $resolving);
        }, $value);
    }
    /**
     * @return bool
     */
    public function isResolved()
    {
        return $this->resolved;
    }
    public function escapeValue(mixed $value) : mixed
    {
        if (\is_string($value)) {
            return \str_replace('%', '%%', $value);
        }
        if (\is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                $result[$k] = $this->escapeValue($v);
            }
            return $result;
        }
        return $value;
    }
    public function unescapeValue(mixed $value) : mixed
    {
        if (\is_string($value)) {
            return \str_replace('%%', '%', $value);
        }
        if (\is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                $result[$k] = $this->unescapeValue($v);
            }
            return $result;
        }
        return $value;
    }
}
