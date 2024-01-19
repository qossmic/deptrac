<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\Argument;

use DEPTRAC_202401\Symfony\Component\DependencyInjection\ContainerBuilder;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Definition;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use DEPTRAC_202401\Symfony\Component\DependencyInjection\Reference;
use DEPTRAC_202401\Symfony\Component\VarExporter\ProxyHelper;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
class LazyClosure
{
    public readonly object $service;
    public function __construct(private \Closure $initializer)
    {
        unset($this->service);
    }
    public function __get(mixed $name) : mixed
    {
        if ('service' !== $name) {
            throw new InvalidArgumentException(\sprintf('Cannot read property "%s" from a lazy closure.', $name));
        }
        if (isset($this->initializer)) {
            $this->service = ($this->initializer)();
            unset($this->initializer);
        }
        return $this->service;
    }
    public static function getCode(string $initializer, array $callable, Definition $definition, ContainerBuilder $container, ?string $id) : string
    {
        $method = $callable[1];
        $asClosure = 'Closure' === ($definition->getClass() ?: 'Closure');
        if ($asClosure) {
            $class = ($callable[0] instanceof Reference ? $container->findDefinition($callable[0]) : $callable[0])->getClass();
        } else {
            $class = $definition->getClass();
        }
        $r = $container->getReflectionClass($class);
        if (null !== $id) {
            $id = \sprintf(' for service "%s"', $id);
        }
        if (!$asClosure) {
            $id = \str_replace('%', '%%', (string) $id);
            if (!$r || !$r->isInterface()) {
                throw new RuntimeException(\sprintf("Cannot create adapter{$id} because \"%s\" is not an interface.", $class));
            }
            if (1 !== \count($method = $r->getMethods())) {
                throw new RuntimeException(\sprintf("Cannot create adapter{$id} because interface \"%s\" doesn't have exactly one method.", $class));
            }
            $method = $method[0]->name;
        } elseif (!$r || !$r->hasMethod($method)) {
            throw new RuntimeException("Cannot create lazy closure{$id} because its corresponding callable is invalid.");
        }
        $methodReflector = $r->getMethod($method);
        $code = ProxyHelper::exportSignature($methodReflector, \true, $args);
        if ($asClosure) {
            $code = ' { ' . \preg_replace('/: static$/', ': \\' . $r->name, $code);
        } else {
            $code = ' implements \\' . $r->name . ' { ' . $code;
        }
        $code = 'new class(' . $initializer . ') extends \\' . self::class . $code . ' { ' . ($methodReflector->hasReturnType() && 'void' === (string) $methodReflector->getReturnType() ? '' : 'return ') . '$this->service->' . $callable[1] . '(' . $args . '); } ' . '}';
        return $asClosure ? '(' . $code . ')->' . $method . '(...)' : $code;
    }
}
