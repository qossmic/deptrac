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
 * Represents a property when building classes.
 *
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Property
{
    private string $name;
    private string $originalName;
    private bool $array = \false;
    private bool $scalarsAllowed = \false;
    private ?string $type = null;
    private ?string $content = null;
    public function __construct(string $originalName, string $name)
    {
        $this->name = $name;
        $this->originalName = $originalName;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getOriginalName() : string
    {
        return $this->originalName;
    }
    public function setType(string $type) : void
    {
        $this->array = \false;
        $this->type = $type;
        if (\str_ends_with($type, '|scalar')) {
            $this->scalarsAllowed = \true;
            $this->type = $type = \substr($type, 0, -7);
        }
        if (\str_ends_with($type, '[]')) {
            $this->array = \true;
            $this->type = \substr($type, 0, -2);
        }
    }
    public function getType() : ?string
    {
        return $this->type;
    }
    public function getContent() : ?string
    {
        return $this->content;
    }
    public function setContent(string $content) : void
    {
        $this->content = $content;
    }
    public function isArray() : bool
    {
        return $this->array;
    }
    public function areScalarsAllowed() : bool
    {
        return $this->scalarsAllowed;
    }
}
