<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Config\Builder;

/**
 * Represents a method when building classes.
 *
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Method
{
    private string $content;
    public function __construct(string $content)
    {
        $this->content = $content;
    }
    public function getContent() : string
    {
        return $this->content;
    }
}
