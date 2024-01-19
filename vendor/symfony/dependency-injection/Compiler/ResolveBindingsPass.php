<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\Compiler;

use DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument\BoundArgument;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Attribute\Target;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\ContainerBuilder;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Definition;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Reference;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\TypedReference;
use DEPTRAC_202401\Symfony\Component\VarExporter\ProxyHelper;
/**
 * @author Guilhem Niot <guilhem.niot@gmail.com>
 */
class ResolveBindingsPass extends AbstractRecursivePass
{
    protected bool $skipScalars = \true;
    private array $usedBindings = [];
    private array $unusedBindings = [];
    private array $errorMessages = [];
    /**
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $this->usedBindings = $container->getRemovedBindingIds();
        try {
            parent::process($container);
            foreach ($this->unusedBindings as [$key, $serviceId, $bindingType, $file]) {
                $argumentType = $argumentName = $message = null;
                if (\str_contains($key, ' ')) {
                    [$argumentType, $argumentName] = \explode(' ', $key, 2);
                } elseif ('$' === $key[0]) {
                    $argumentName = $key;
                } else {
                    $argumentType = $key;
                }
                if ($argumentType) {
                    $message .= \sprintf('of type "%s" ', $argumentType);
                }
                if ($argumentName) {
                    $message .= \sprintf('named "%s" ', $argumentName);
                }
                if (BoundArgument::DEFAULTS_BINDING === $bindingType) {
                    $message .= 'under "_defaults"';
                } elseif (BoundArgument::INSTANCEOF_BINDING === $bindingType) {
                    $message .= 'under "_instanceof"';
                } else {
                    $message .= \sprintf('for service "%s"', $serviceId);
                }
                if ($file) {
                    $message .= \sprintf(' in file "%s"', $file);
                }
                $message = \sprintf('A binding is configured for an argument %s, but no corresponding argument has been found. It may be unused and should be removed, or it may have a typo.', $message);
                if ($this->errorMessages) {
                    $message .= \sprintf("\nCould be related to%s:", 1 < \count($this->errorMessages) ? ' one of' : '');
                }
                foreach ($this->errorMessages as $m) {
                    $message .= "\n - " . $m;
                }
                throw new InvalidArgumentException($message);
            }
        } finally {
            $this->usedBindings = [];
            $this->unusedBindings = [];
            $this->errorMessages = [];
        }
    }
    protected function processValue(mixed $value, bool $isRoot = \false) : mixed
    {
        if ($value instanceof TypedReference && $value->getType() === (string) $value) {
            // Already checked
            $bindings = $this->container->getDefinition($this->currentId)->getBindings();
            $name = $value->getName();
            if (isset($name, $bindings[$name = $value . ' $' . $name])) {
                return $this->getBindingValue($bindings[$name]);
            }
            if (isset($bindings[$value->getType()])) {
                return $this->getBindingValue($bindings[$value->getType()]);
            }
            return parent::processValue($value, $isRoot);
        }
        if (!$value instanceof Definition || !($bindings = $value->getBindings())) {
            return parent::processValue($value, $isRoot);
        }
        $bindingNames = [];
        foreach ($bindings as $key => $binding) {
            [$bindingValue, $bindingId, $used, $bindingType, $file] = $binding->getValues();
            if ($used) {
                $this->usedBindings[$bindingId] = \true;
                unset($this->unusedBindings[$bindingId]);
            } elseif (!isset($this->usedBindings[$bindingId])) {
                $this->unusedBindings[$bindingId] = [$key, $this->currentId, $bindingType, $file];
            }
            if (\preg_match('/^(?:(?:array|bool|float|int|string|iterable|([^ $]++)) )\\$/', $key, $m)) {
                $bindingNames[\substr($key, \strlen($m[0]))] = $binding;
            }
            if (!isset($m[1])) {
                continue;
            }
            if (\is_subclass_of($m[1], \UnitEnum::class)) {
                $bindingNames[\substr($key, \strlen($m[0]))] = $binding;
                continue;
            }
            if (null !== $bindingValue && !$bindingValue instanceof Reference && !$bindingValue instanceof Definition && !$bindingValue instanceof TaggedIteratorArgument && !$bindingValue instanceof ServiceLocatorArgument) {
                throw new InvalidArgumentException(\sprintf('Invalid value for binding key "%s" for service "%s": expected "%s", "%s", "%s", "%s" or null, "%s" given.', $key, $this->currentId, Reference::class, Definition::class, TaggedIteratorArgument::class, ServiceLocatorArgument::class, \get_debug_type($bindingValue)));
            }
        }
        if ($value->isAbstract()) {
            return parent::processValue($value, $isRoot);
        }
        $calls = $value->getMethodCalls();
        try {
            if ($constructor = $this->getConstructor($value, \false)) {
                $calls[] = [$constructor, $value->getArguments()];
            }
        } catch (RuntimeException $e) {
            $this->errorMessages[] = $e->getMessage();
            $this->container->getDefinition($this->currentId)->addError($e->getMessage());
            return parent::processValue($value, $isRoot);
        }
        foreach ($calls as $i => $call) {
            [$method, $arguments] = $call;
            if ($method instanceof \ReflectionFunctionAbstract) {
                $reflectionMethod = $method;
            } else {
                try {
                    $reflectionMethod = $this->getReflectionMethod($value, $method);
                } catch (RuntimeException $e) {
                    if ($value->getFactory()) {
                        continue;
                    }
                    throw $e;
                }
            }
            $names = [];
            foreach ($reflectionMethod->getParameters() as $key => $parameter) {
                $names[$key] = $parameter->name;
                if (\array_key_exists($key, $arguments) && '' !== $arguments[$key]) {
                    continue;
                }
                if (\array_key_exists($parameter->name, $arguments) && '' !== $arguments[$parameter->name]) {
                    continue;
                }
                $typeHint = \ltrim(ProxyHelper::exportType($parameter) ?? '', '?');
                $name = Target::parseName($parameter, parsedName: $parsedName);
                if ($typeHint && (\array_key_exists($k = \preg_replace('/(^|[(|&])\\\\/', '\\1', $typeHint) . ' $' . $name, $bindings) || \array_key_exists($k = \preg_replace('/(^|[(|&])\\\\/', '\\1', $typeHint) . ' $' . $parsedName, $bindings))) {
                    $arguments[$key] = $this->getBindingValue($bindings[$k]);
                    continue;
                }
                if (\array_key_exists($k = '$' . $name, $bindings) || \array_key_exists($k = '$' . $parsedName, $bindings)) {
                    $arguments[$key] = $this->getBindingValue($bindings[$k]);
                    continue;
                }
                if ($typeHint && '\\' === $typeHint[0] && isset($bindings[$typeHint = \substr($typeHint, 1)])) {
                    $arguments[$key] = $this->getBindingValue($bindings[$typeHint]);
                    continue;
                }
                if (isset($bindingNames[$name]) || isset($bindingNames[$parsedName]) || isset($bindingNames[$parameter->name])) {
                    $bindingKey = \array_search($binding, $bindings, \true);
                    $argumentType = \substr($bindingKey, 0, \strpos($bindingKey, ' '));
                    $this->errorMessages[] = \sprintf('Did you forget to add the type "%s" to argument "$%s" of method "%s::%s()"?', $argumentType, $parameter->name, $reflectionMethod->class, $reflectionMethod->name);
                }
            }
            foreach ($names as $key => $name) {
                if (\array_key_exists($name, $arguments) && (0 === $key || \array_key_exists($key - 1, $arguments))) {
                    $arguments[$key] = $arguments[$name];
                    unset($arguments[$name]);
                }
            }
            if ($arguments !== $call[1]) {
                \ksort($arguments, \SORT_NATURAL);
                $calls[$i][1] = $arguments;
            }
        }
        if ($constructor) {
            [, $arguments] = \array_pop($calls);
            if ($arguments !== $value->getArguments()) {
                $value->setArguments($arguments);
            }
        }
        if ($calls !== $value->getMethodCalls()) {
            $value->setMethodCalls($calls);
        }
        return parent::processValue($value, $isRoot);
    }
    private function getBindingValue(BoundArgument $binding) : mixed
    {
        [$bindingValue, $bindingId] = $binding->getValues();
        $this->usedBindings[$bindingId] = \true;
        unset($this->unusedBindings[$bindingId]);
        return $bindingValue;
    }
}
