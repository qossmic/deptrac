<?php

declare(strict_types = 1);

namespace Tests\Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\Fixtures;

class ClassFoo {

}

namespace Tests\Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\Fixtures2;

use Tests\Qossmic\Deptrac\AstRunner\AstParser\NikicPhpParser\Fixtures\ClassFoo;

class ClassBar extends ClassFoo {

}
