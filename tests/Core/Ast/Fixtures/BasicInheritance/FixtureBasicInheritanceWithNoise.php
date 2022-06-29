<?php

namespace Tests\Qossmic\Deptrac\Core\Ast\Fixtures\BasicInheritance;

use Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceWithNoiseFoo1 as foo1;

final class FixtureBasicInheritanceWithNoiseFoo1 {}
final class FixtureBasicInheritanceWithNoiseFoo2 {}

final class FixtureBasicInheritanceWithNoiseA {

    function a(foo1 $a) {
        new foo1();
        new FixtureBasicInheritanceWithNoiseFoo1();
    }

}
final class FixtureBasicInheritanceWithNoiseB extends FixtureBasicInheritanceWithNoiseA { }
final class FixtureBasicInheritanceWithNoiseC extends FixtureBasicInheritanceWithNoiseB
{ }

new FixtureBasicInheritanceWithNoiseFoo2();
