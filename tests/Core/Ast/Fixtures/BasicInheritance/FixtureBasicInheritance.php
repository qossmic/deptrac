<?php

namespace Tests\Qossmic\Deptrac\Core\Ast\Fixtures;

final class FixtureBasicInheritanceA { }
final class FixtureBasicInheritanceB extends FixtureBasicInheritanceA { }
final class FixtureBasicInheritanceC extends FixtureBasicInheritanceB { }
final class FixtureBasicInheritanceD extends FixtureBasicInheritanceC { }
final class FixtureBasicInheritanceE extends \Tests\Qossmic\Deptrac\Core\Ast\Fixtures\FixtureBasicInheritanceD
{ }
