<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Yaml\Tag;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Guilhem N. <egetick@gmail.com>
 */
final class TaggedValue
{
    private string $tag;
    private mixed $value;
    public function __construct(string $tag, mixed $value)
    {
        $this->tag = $tag;
        $this->value = $value;
    }
    public function getTag() : string
    {
        return $this->tag;
    }
    public function getValue() : mixed
    {
        return $this->value;
    }
}
