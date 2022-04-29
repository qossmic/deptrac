<?php

namespace Tests\Qossmic\Deptrac\Ast\Fixtures;

final class FixtureBasicInheritanceA { }
final class FixtureBasicInheritanceB extends FixtureBasicInheritanceA { }
final class FixtureBasicInheritanceC extends FixtureBasicInheritanceB { }
final class FixtureBasicInheritanceD extends FixtureBasicInheritanceC { }
final class FixtureBasicInheritanceE extends \Tests\Qossmic\Deptrac\Ast\Fixtures\FixtureBasicInheritanceD
{ }
