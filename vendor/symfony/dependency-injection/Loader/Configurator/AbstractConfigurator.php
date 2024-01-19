<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\Loader\Configurator;

use DEPTRAC_202401\Symfony\Component\Config\Loader\ParamConfigurator;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Alias;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Definition;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Parameter;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Reference;
use DEPTRAC_202401\Symfony\Component\ExpressionLanguage\Expression;
abstract class AbstractConfigurator
{
    public const FACTORY = 'unknown';
    /**
     * @var \Closure(mixed, bool):mixed|null
     */
    public static $valuePreProcessor;
    /** @internal */
    protected Definition|Alias|null $definition = null;
    /**
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if (\method_exists($this, 'set' . $method)) {
            return $this->{'set' . $method}(...$args);
        }
        throw new \BadMethodCallException(\sprintf('Call to undefined method "%s::%s()".', static::class, $method));
    }
    public function __sleep() : array
    {
        throw new \BadMethodCallException('Cannot serialize ' . __CLASS__);
    }
    /**
     * @return void
     */
    public function __wakeup()
    {
        throw new \BadMethodCallException('Cannot unserialize ' . __CLASS__);
    }
    /**
     * Checks that a value is valid, optionally replacing Definition and Reference configurators by their configure value.
     *
     * @param bool $allowServices whether Definition and Reference are allowed; by default, only scalars, arrays and enum are
     *
     * @return mixed the value, optionally cast to a Definition/Reference
     */
    public static function processValue(mixed $value, bool $allowServices = \false) : mixed
    {
        if (\is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = static::processValue($v, $allowServices);
            }
            return self::$valuePreProcessor ? (self::$valuePreProcessor)($value, $allowServices) : $value;
        }
        if (self::$valuePreProcessor) {
            $value = (self::$valuePreProcessor)($value, $allowServices);
        }
        if ($value instanceof ReferenceConfigurator) {
            $reference = new Reference($value->id, $value->invalidBehavior);
            return $value instanceof ClosureReferenceConfigurator ? new ServiceClosureArgument($reference) : $reference;
        }
        if ($value instanceof InlineServiceConfigurator) {
            $def = $value->definition;
            $value->definition = null;
            return $def;
        }
        if ($value instanceof ParamConfigurator) {
            return (string) $value;
        }
        if ($value instanceof self) {
            throw new InvalidArgumentException(\sprintf('"%s()" can be used only at the root of service configuration files.', $value::FACTORY));
        }
        switch (\true) {
            case null === $value:
            case \is_scalar($value):
            case $value instanceof \UnitEnum:
                return $value;
            case $value instanceof ArgumentInterface:
            case $value instanceof Definition:
            case $value instanceof Expression:
            case $value instanceof Parameter:
            case $value instanceof AbstractArgument:
            case $value instanceof Reference:
                if ($allowServices) {
                    return $value;
                }
        }
        throw new InvalidArgumentException(\sprintf('Cannot use values of type "%s" in service configuration files.', \get_debug_type($value)));
    }
}
