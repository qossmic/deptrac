<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Config\Definition;

use DEPTRAC_202401\Symfony\Component\Config\Definition\Exception\InvalidTypeException;
/**
 * This node represents a scalar value in the config tree.
 *
 * The following values are considered scalars:
 *   * booleans
 *   * strings
 *   * null
 *   * integers
 *   * floats
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ScalarNode extends VariableNode
{
    /**
     * @return void
     */
    protected function validateType(mixed $value)
    {
        if (!\is_scalar($value) && null !== $value) {
            $ex = new InvalidTypeException(\sprintf('Invalid type for path "%s". Expected "scalar", but got "%s".', $this->getPath(), \get_debug_type($value)));
            if ($hint = $this->getInfo()) {
                $ex->addHint($hint);
            }
            $ex->setPath($this->getPath());
            throw $ex;
        }
    }
    protected function isValueEmpty(mixed $value) : bool
    {
        // assume environment variables are never empty (which in practice is likely to be true during runtime)
        // not doing so breaks many configs that are valid today
        if ($this->isHandlingPlaceholder()) {
            return \false;
        }
        return null === $value || '' === $value;
    }
    protected function getValidPlaceholderTypes() : array
    {
        return ['bool', 'int', 'float', 'string'];
    }
}
