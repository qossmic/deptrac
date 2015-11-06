<?php 

namespace DependencyTracker\Tests;

trait ArrayAsserts
{

    public function assertArrayValuesEquals(array $expected, array $value)
    {
        $expected = array_values($expected);

        $value = array_map(function($v) {
            if (is_object($v)) {
                return $v->__toString();
            }

            return $v;
        }, $value);

        $value = array_values($value);

        sort($expected);
        sort($value);

        $this->assertEquals($expected, $value);
    }

}
