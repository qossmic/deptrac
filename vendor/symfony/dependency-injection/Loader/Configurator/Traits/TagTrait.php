<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
trait TagTrait
{
    /**
     * Adds a tag for this definition.
     *
     * @return $this
     */
    public final function tag(string $name, array $attributes = []) : static
    {
        if ('' === $name) {
            throw new InvalidArgumentException(\sprintf('The tag name for service "%s" must be a non-empty string.', $this->id));
        }
        $this->validateAttributes($name, $attributes);
        $this->definition->addTag($name, $attributes);
        return $this;
    }
    private function validateAttributes(string $tag, array $attributes, array $path = []) : void
    {
        foreach ($attributes as $name => $value) {
            if (\is_array($value)) {
                $this->validateAttributes($tag, $value, [...$path, $name]);
            } elseif (!\is_scalar($value ?? '')) {
                $name = \implode('.', [...$path, $name]);
                throw new InvalidArgumentException(\sprintf('A tag attribute must be of a scalar-type or an array of scalar-types for service "%s", tag "%s", attribute "%s".', $this->id, $tag, $name));
            }
        }
    }
}
